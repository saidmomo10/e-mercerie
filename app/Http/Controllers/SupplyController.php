<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supply;

class SupplyController extends Controller
{
    // Recherche AJAX (live)
    public function searchAjax(Request $request)
    {
        $query = $request->input('search');
        $supplies = Supply::when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })->get();
        return response()->json($supplies);
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
