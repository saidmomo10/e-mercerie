<?php

namespace App\Http\Controllers;

use App\Models\MerchantSupply;
use App\Models\Supply;
use Illuminate\Http\Request;

class MerchantSupplyController extends Controller
{
    // Affiche toutes les fournitures du marchand
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = MerchantSupply::with('supply')
            ->where('user_id', $request->user()->id)
            ->latest();

        if ($search) {
            $query->whereHas('supply', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $merchantSupplies = $query->paginate(1)->withQueryString();

        // If AJAX request, return rendered partials for rows and pagination
        if ($request->ajax()) {
            $rows = view('merchant.supplies._rows', compact('merchantSupplies'))->render();
            $pagination = view('merchant.supplies._pagination', compact('merchantSupplies'))->render();
            return response()->json(['rows' => $rows, 'pagination' => $pagination]);
        }

        return view('merchant.supplies.index', compact('merchantSupplies', 'search'));
    }

    // Formulaire pour ajouter une nouvelle fourniture
    public function create(Request $request)
    {
        $user = $request->user();

        if (!$user->city || !$user->phone || !$user->address) {
            return redirect()->route('merchant.supplies.index')
                ->with('showProfileModal', true);
        }

        // Charger seulement les fournitures nécessaires pour l'initialisation
        $supplies = Supply::limit(50)->get();
        return view('merchant.supplies.create', compact('supplies'));
    }

    // Ajoutez cette méthode pour la recherche AJAX
    public function searchSupplies(Request $request)
    {
        $search = $request->get('q');
        $supplies = Supply::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
            ->limit(20)
            ->get(['id', 'name as text']);

        return response()->json(['results' => $supplies]);
    }

    // Stocke la nouvelle fourniture
    public function store(Request $request)
    {
        $data = $request->validate([
            'supply_id' => 'required|exists:supplies,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $existing = MerchantSupply::where('user_id', $request->user()->id)
            ->where('supply_id', $data['supply_id'])
            ->first();

        if ($existing) {
            $existing->update($data);
            return redirect()->route('merchant.supplies.index')
                ->with('success', 'Fourniture déjà existante, mise à jour avec succès');
        }

        MerchantSupply::create([
            'user_id' => $request->user()->id,
            'supply_id' => $data['supply_id'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
        ]);

        return redirect()->route('merchant.supplies.index')
            ->with('success', 'Fourniture ajoutée à votre boutique');
    }

    // Formulaire pour éditer une fourniture
    public function edit($id, Request $request)
    {
        $merchantSupply = MerchantSupply::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $supplies = Supply::all();
        return view('merchant.supplies.edit', compact('merchantSupply', 'supplies'));
    }

    // Mise à jour d'une fourniture
    public function update(Request $request, $id)
    {
        $merchantSupply = MerchantSupply::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $merchantSupply->update($data);

        return redirect()->route('merchant.supplies.index')
            ->with('success', 'Fourniture mise à jour avec succès');
    }

    // Suppression d'une fourniture
    public function destroy(Request $request, $id)
    {
        $merchantSupply = MerchantSupply::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $merchantSupply->delete();

        return redirect()->route('merchant.supplies.index')
            ->with('success', 'Fourniture supprimée avec succès');
    }
}
