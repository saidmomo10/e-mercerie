<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supply;

class SupplyController extends Controller
{
    // Recherche AJAX (live)
    public function searchAjax(Request $request)
    {
        // Compatibilité: search, query, q
        $query = $request->input('search')
            ?? $request->input('query')
            ?? $request->input('q');

        $supplies = Supply::when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(20) // évite surcharge en production
            ->get();

        $payload = $supplies->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description,
                'price' => $s->price,
                'image_url' => $s->image_url,   // important !
            ];
        });

        return response()->json($payload);
    }

    // Étape 1 : Liste des fournitures
    public function index()
    {
        $supplies = Supply::all();
        return view('supplies.index', compact('supplies'));
    }

    // Étape 2 : Formulaire de sélection
    public function selectionForm()
    {
        $supplies = Supply::all();
        return view('supplies.selection', compact('supplies'));
    }

    public function search(Request $request)
    {
        $query = $request->input('search');

        $supplies = Supply::when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%");
        })->get();

        return view('supplies.selection', compact('supplies'));
    }

}
