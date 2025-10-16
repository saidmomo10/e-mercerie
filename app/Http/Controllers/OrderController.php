<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MerchantSupply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Affiche les commandes selon le rôle (Couturier / Mercerie)
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isCouturier()) {
            // Commandes passées par le couturier
            $orders = $user->ordersAsCouturier()->with(['items.supply', 'mercerie'])->latest()->get();
        } elseif ($user->isMercerie()) {
            // Commandes reçues par la mercerie
            $orders = $user->ordersAsMercerie()->with(['items.supply', 'couturier'])->latest()->get();
        } else {
            $orders = collect();
        }

        return view('orders.index', compact('orders'));
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

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Commande créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}
