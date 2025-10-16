<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supply;

class SupplyController extends Controller
{
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
}
