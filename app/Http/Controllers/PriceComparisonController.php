<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PriceComparisonController extends Controller
{
    // Étape 3 : Comparaison des merceries pour Blade
    public function compare(Request $request)
    {
        $itemsInput = $request->input('items', []);
        $items = [];

        foreach ($itemsInput as $supplyId => $data) {
            if (isset($data['quantity']) && $data['quantity'] > 0) {
                $items[] = [
                    'supply_id' => $supplyId,
                    'quantity' => $data['quantity']
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()->with('error', 'Veuillez sélectionner au moins une fourniture.');
        }

        $merceries = User::where('role', 'mercerie')->with('merchantSupplies.supply')->get();
        $disponibles = [];
        $non_disponibles = [];

        foreach ($merceries as $mercerie) {
            $total = 0;
            $details = [];
            $peut_fournir = true;
            $raisons = [];

            foreach ($items as $item) {
                $supply = $mercerie->merchantSupplies->firstWhere('supply_id', $item['supply_id']);

                if (!$supply) {
                    $peut_fournir = false;
                    $raisons[] = "Fourniture ID {$item['supply_id']} non disponible";
                    continue;
                }

                if ($supply->stock_quantity < $item['quantity']) {
                    $peut_fournir = false;
                    $raisons[] = "Stock insuffisant pour '{$supply->supply->name}' (disponible: {$supply->stock_quantity})";
                    continue;
                }

                $sous_total = $supply->price * $item['quantity'];
                $total += $sous_total;

                $details[] = [
                    'supply' => $supply->supply->name,
                    'prix_unitaire' => (float) $supply->price,
                    'quantite' => $item['quantity'],
                    'sous_total' => $sous_total,
                    'merchant_supply_id' => $supply->id,
                ];
            }

            if ($peut_fournir) {
                $disponibles[] = [
                    'mercerie' => [
                        'id' => $mercerie->id,
                        'name' => $mercerie->name,
                    ],
                    'total_estime' => $total,
                    'details' => $details,
                ];
            } else {
                $non_disponibles[] = [
                    'mercerie' => [
                        'id' => $mercerie->id,
                        'name' => $mercerie->name,
                    ],
                    'raisons' => $raisons
                ];
            }
        }

        usort($disponibles, fn($a, $b) => $a['total_estime'] <=> $b['total_estime']);

        return view('merceries.compare', compact('disponibles', 'non_disponibles', 'items'));
    }
}
