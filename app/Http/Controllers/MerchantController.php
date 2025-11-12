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

    /**
     * Public landing page showing merceries with supplies
     */
    public function landing(Request $request)
    {
        $search = $request->input('search');

        $merceries = User::where('role', 'mercerie')
            ->whereHas('merchantSupplies')
            ->when(auth()->check(), function ($q) {
                $q->where('id', '!=', auth()->id());
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->with('merchantSupplies')
            ->get();

        return view('landing', compact('merceries', 'search'));
    }

    public function searchAjax(Request $request)
    {
        $query = $request->input('search');

        $merceries = User::where('role', 'mercerie')
            // Only merceries (role) that have supplies
            ->whereHas('merchantSupplies')
            // Exclude currently authenticated merchant if present
            ->when(auth()->check(), function ($q) {
                $q->where('id', '!=', auth()->id());
            })
            // Apply search filter when provided
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('city', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%");
                });
            })
            ->with('merchantSupplies')
            ->get();

        // Map response to include avatar_url and a short description
        $payload = $merceries->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'city' => $m->city,
                'phone' => $m->phone,
                'avatar_url' => $m->avatar_url ?? asset('images/defaults/mercerie-avatar.png'),
                'description' => $m->address ? 
                    (strlen($m->address) > 80 ? substr($m->address, 0, 77) . '...' : $m->address) : '',
                'has_supplies' => $m->merchantSupplies->isNotEmpty(),
            ];
        });

        return response()->json($payload);
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
