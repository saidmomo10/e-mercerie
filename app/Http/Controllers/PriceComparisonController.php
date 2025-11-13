<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PriceComparisonController extends Controller
{
    // Étape 3 : Comparaison des merceries pour Blade
    public function compare(Request $request)
    {
        // Validate optional location filters
        $data = $request->validate([
            'city_id' => 'nullable|exists:cities,id',
            'quarter_id' => 'nullable|exists:quarters,id',
        ]);

        // If both provided, ensure the quarter belongs to the city
        if (!empty($data['city_id']) && !empty($data['quarter_id'])) {
            $belongs = \App\Models\Quarter::where('id', $data['quarter_id'])->where('city_id', $data['city_id'])->exists();
            if (! $belongs) {
                return redirect()->back()->with('error', 'Le quartier sélectionné n\'appartient pas à la ville choisie.');
            }
        }
        $itemsInput = $request->input('items', []);
        $items = [];

        foreach ($itemsInput as $supplyId => $itemData) {
            if (isset($itemData['quantity']) && $itemData['quantity'] > 0) {
                $items[] = [
                    'supply_id' => $supplyId,
                    'quantity' => $itemData['quantity']
                ];
            }
        }

        if (empty($items)) {
            return redirect()->back()->with('error', 'Veuillez sélectionner au moins une fourniture.');
        }

        // Charger les merceries avec leurs fournitures et les infos de la fourniture liée
        $merceriesQuery = User::where('role', 'mercerie')->with('merchantSupplies.supply');

        // Apply optional filters
        if (!empty($data['city_id'])) {
            $merceriesQuery->where('city_id', $data['city_id']);
        }
        if (!empty($data['quarter_id'])) {
            $merceriesQuery->where('quarter_id', $data['quarter_id']);
        }

        $merceries = $merceriesQuery->get();
        $disponibles = [];
        $non_disponibles = [];

        foreach ($merceries as $mercerie) {
            $total = 0;
            $details = [];
            $peut_fournir = true;
            $raisons = [];

            foreach ($items as $item) {
                $supply = $mercerie->merchantSupplies->firstWhere('supply_id', $item['supply_id']);

                // Récupération du nom de la fourniture depuis la table supplies
                $supplyName = \App\Models\Supply::find($item['supply_id'])->name ?? "Fourniture inconnue";

                if (!$supply) {
                    $peut_fournir = false;
                    $raisons[] = "La fourniture « {$supplyName} » n’est pas disponible chez cette mercerie.";
                    continue;
                }

                if ($supply->stock_quantity < $item['quantity']) {
                    $peut_fournir = false;
                    $raisons[] = "⚠️ Stock insuffisant pour « {$supplyName} » (disponible : {$supply->stock_quantity}).";
                    continue;
                }

                $sous_total = $supply->price * $item['quantity'];
                $total += $sous_total;

                $details[] = [
                    'supply' => $supplyName,
                    'prix_unitaire' => (float) $supply->price,
                    'quantite' => $item['quantity'],
                    'sous_total' => $sous_total,
                    'merchant_supply_id' => $supply->id,
                ];
            }

            $mercerieInfo = [
                'id' => $mercerie->id,
                'name' => $mercerie->name,
                'city_name' => $mercerie->cityModel?->name ?? null,
                'quarter_name' => $mercerie->quarter?->name ?? null,
            ];

            if ($peut_fournir) {
                $disponibles[] = [
                    'mercerie' => $mercerieInfo,
                    'total_estime' => $total,
                    'details' => $details,
                ];
            } else {
                $non_disponibles[] = [
                    'mercerie' => $mercerieInfo,
                    'raisons' => $raisons
                ];
            }
        }

    // Trier les merceries disponibles par prix total estimé croissant
    usort($disponibles, fn($a, $b) => $a['total_estime'] <=> $b['total_estime']);

    // Attach selected city/quarter (models) for the view to display
    $selectedCity = !empty($data['city_id']) ? \App\Models\City::find($data['city_id']) : null;
    $selectedQuarter = !empty($data['quarter_id']) ? \App\Models\Quarter::find($data['quarter_id']) : null;

    return view('merceries.compare', compact('disponibles', 'non_disponibles', 'items', 'selectedCity', 'selectedQuarter'));
    }

}
