<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\MerchantSupply;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderAccepted;
use App\Notifications\OrderRejected;
use App\Notifications\NewOrderReceived;

class OrderController extends Controller
{
    /**
     * Affiche les commandes selon le rôle (Couturier / Mercerie)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = null;

        if ($user->isCouturier()) {
            $query = $user->ordersAsCouturier()
                ->with(['items.merchantSupply', 'mercerie'])
                ->latest();
        } elseif ($user->isMercerie()) {
            $query = $user->ordersAsMercerie()
                ->with(['items.merchantSupply', 'couturier'])
                ->latest();
        }

        if ($query && $search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('mercerie', fn($m) => $m->where('name', 'like', "%$search%"))
                ->orWhere('id', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
            });
        }

        if ($query && ($startDate || $endDate)) {
            $query->whereBetween('created_at', [
                $startDate ? date('Y-m-d 00:00:00', strtotime($startDate)) : '2000-01-01 00:00:00',
                $endDate ? date('Y-m-d 23:59:59', strtotime($endDate)) : now(),
            ]);
        }

        $orders = $query ? $query->paginate(2)->appends($request->query()) : collect();

        return view('orders.index', compact('orders', 'search', 'startDate', 'endDate'));
    }




    /**
     * Création d'une commande depuis le formulaire Web
     */
     public function storeWeb(Request $request)
    {
        $request->validate([
            'mercerie_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.merchant_supply_id' => 'required|exists:merchant_supplies,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        if (!$user->isCouturier()) {
            return redirect()->back()->with('error', 'Seuls les couturiers peuvent créer une commande.');
        }

        DB::beginTransaction();

        try {
            $total = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $merchantSupply = MerchantSupply::findOrFail($item['merchant_supply_id']);

                if ($item['quantity'] > $merchantSupply->stock_quantity) {
                    return redirect()->back()->with('error', "Stock insuffisant pour la fourniture ID {$merchantSupply->id}");
                }

                $price = $merchantSupply->price;
                $subtotal = $price * $item['quantity'];
                $total += $subtotal;

                $itemsData[] = [
                    'supply_id' => $merchantSupply->supply_id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ];

                $merchantSupply->decrement('stock_quantity', $item['quantity']);
            }

            $order = Order::create([
                'couturier_id' => $user->id,
                'mercerie_id' => $request->mercerie_id,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            foreach ($itemsData as $data) {
                $data['order_id'] = $order->id;
                OrderItem::create($data);
            }

            // Notification à la mercerie
            $mercerie = $order->mercerie;
            $mercerie->notify(new NewOrderReceived($order));

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Commande créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function storeFromMerchant(Request $request, $mercerieId)
    {
        $user = auth()->user();

        if (!$user->isCouturier()) {
            abort(403, 'Seuls les couturiers peuvent passer des commandes.');
        }

        $items = $request->input('items', []);

        if (empty($items)) {
            return back()->with('error', 'Veuillez sélectionner au moins une fourniture.');
        }

        $total = 0;

        // Création de la commande
        $order = $user->ordersAsCouturier()->create([
            'mercerie_id' => $mercerieId,
            'total_amount' => 0,
            'status' => 'pending',
        ]);

        // Ajout des éléments de commande
        foreach ($items as $item) {
            if (
                isset($item['merchant_supply_id']) &&
                isset($item['quantity']) &&
                $item['quantity'] > 0
            ) {
                $merchantSupply = MerchantSupply::find($item['merchant_supply_id']);

                if ($merchantSupply) {
                    $subtotal = $merchantSupply->price * $item['quantity'];

                    $order->items()->create([
                        'merchant_supply_id' => $merchantSupply->id,
                        'quantity' => $item['quantity'],
                        'price' => $merchantSupply->price,
                        'subtotal' => $subtotal,
                    ]);

                    $total += $subtotal;
                }
            }
        }

        // Mise à jour du total
        $order->update(['total_amount' => $total]);

        // Notification à la mercerie
        try {
            $mercerie = $order->mercerie;
            if ($mercerie) {
                $notification = new NewOrderReceived($order);
                $mercerie->notify($notification);
                
                // 🔥 ENVOYER LES NOTIFICATIONS WEB PUSH À LA MERCERIE
                $notification->sendWebPushNotification($mercerie);
            }
        } catch (\Exception $e) {
            logger()->error('Erreur lors de l\'envoi de la notification NewOrderReceived: ' . $e->getMessage());
        }

        return redirect()->route('orders.index')->with('success', 'Commande effectuée avec succès.');
    }

    /**
     * 📦 Afficher le détail d’une commande spécifique
     */
    public function show($id)
    {
        $order = Order::with(['items.merchantSupply', 'mercerie', 'couturier'])
            ->findOrFail($id);

        // Vérifier que l'utilisateur est autorisé à la voir
        if (auth()->id() !== $order->couturier_id && auth()->id() !== $order->mercerie_id) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function preview(Request $request, $mercerieId)
    {
        $mercerie = \App\Models\User::findOrFail($mercerieId);

        if (!$mercerie->isMercerie()) {
            return redirect()->back()->with('error', 'L’utilisateur sélectionné n’est pas une mercerie valide.');
        }

        $items = collect($request->items)->filter(fn($item) => $item['quantity'] > 0);

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'Veuillez sélectionner au moins une fourniture.');
        }

        $details = $items->map(function ($item) {
            $supply = \App\Models\MerchantSupply::with('supply')->find($item['merchant_supply_id']);
            $sous_total = $item['quantity'] * $supply->price;

            return [
                'merchant_supply_id' => $supply->id,
                'supply' => $supply->supply->name,
                'quantity' => $item['quantity'],
                'price' => $supply->price,
                'subtotal' => $sous_total,
            ];
        });

        $total = $details->sum('subtotal');

        return view('orders.preview', compact('mercerie', 'details', 'total'));
    }



     public function accept($id)
    {
        $order = Order::with(['items.merchantSupply.supply'])->findOrFail($id);
        $user = auth()->user();

        if ($order->mercerie_id !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Cette commande a déjà été traitée.');
        }

        DB::beginTransaction();

        try {
            foreach ($order->items as $item) {
                $merchantSupply = $item->merchantSupply;

                if ($merchantSupply->stock_quantity < $item->quantity) {
                    $supplyName = $merchantSupply->supply->name ?? 'Fourniture inconnue';
                    throw new \Exception("Stock insuffisant pour '{$supplyName}'. Stock disponible: {$merchantSupply->stock_quantity}, Quantité demandée: {$item->quantity}");
                }
            }

            foreach ($order->items as $item) {
                $merchantSupply = $item->merchantSupply;
                $merchantSupply->decrement('stock_quantity', $item->quantity);

                // Notification stock faible si nécessaire
                if ($merchantSupply->stock_quantity <= 5) {
                    $user->notify(new LowStockAlert($merchantSupply));
                }
            }

            $order->update(['status' => 'confirmed']);

            // Notification au couturier
            $notification = new OrderAccepted($order);
            $order->couturier->notify($notification);

            // 🔥 ENVOYER LES NOTIFICATIONS WEB PUSH
            $notification->sendWebPushNotification($order->couturier);

            DB::commit();

            return back()->with('success', 'Commande acceptée et stock mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erreur lors de l'acceptation de la commande", [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
 * Vérifie la disponibilité du stock pour une commande
 */
private function checkStockAvailability(Order $order)
{
    $unavailableItems = [];
    
    foreach ($order->items as $item) {
        $merchantSupply = $item->merchantSupply;
        
        if ($merchantSupply->stock_quantity < $item->quantity) {
            $unavailableItems[] = [
                'supply_name' => $merchantSupply->supply->name,
                'available' => $merchantSupply->stock_quantity,
                'requested' => $item->quantity
            ];
        }
    }
    
    return $unavailableItems;
}



public function reject($id)
{
    $order = Order::findOrFail($id);
    $user = auth()->user();

    if ($order->mercerie_id !== $user->id) {
        abort(403, 'Action non autorisée.');
    }

    if ($order->status !== 'pending') {
        return back()->with('error', 'Cette commande a déjà été traitée.');
    }

    $order->update(['status' => 'cancelled']);

    // Notification au couturier
    $notification = new OrderRejected($order);
    $order->couturier->notify($notification);

    // 🔥 ENVOYER LES NOTIFICATIONS WEB PUSH
    $notification->sendWebPushNotification($order->couturier);

    return back()->with('success', 'Commande rejetée avec succès.');
}



}
