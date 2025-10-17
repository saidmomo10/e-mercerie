<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    // Liste de toutes les merceries
    public function index(Request $request)
    {
        $search = $request->input('search');

        $merceries = User::where('role', 'mercerie')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->get();

        return view('couturier.merceries.index', compact('merceries', 'search'));
    }


    // Détails d'une mercerie + fournitures disponibles
    public function show($id)
    {
        $mercerie = User::where('role', 'mercerie')->with('merchantSupplies.supply')->findOrFail($id);
        return view('couturier.merceries.show', compact('mercerie'));
    }
}
