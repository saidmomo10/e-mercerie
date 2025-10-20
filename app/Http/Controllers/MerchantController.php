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
            ->where('id', '!=', auth()->id()) // exclure la mercerie connectée
            ->whereHas('merchantSupplies')   // au moins une fourniture
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->get();

        return view('couturier.merceries.index', compact('merceries', 'search'));
    }

    public function searchAjax(Request $request)
    {
        $query = $request->input('search');

        $merceries = User::where('role', 'mercerie')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('city', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%");
                });
            })
            ->get(['id', 'name', 'city', 'phone']);

        return response()->json($merceries);
    }




    // Détails d'une mercerie + fournitures disponibles
    public function show($id)
    {
        $mercerie = User::where('role', 'mercerie')->with('merchantSupplies.supply')->findOrFail($id);
        return view('couturier.merceries.show', compact('mercerie'));
    }

    public function edit()
    {
        $mercerie = auth()->user();
        return view('merceries.profile.edit', compact('mercerie'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('merchant.supplies.index')->with('success', 'Profil complété avec succès.');
    }

}
