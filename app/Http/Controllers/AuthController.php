<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Inscription
    // Afficher le formulaire d'inscription
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Inscription via Blade
    public function registerWeb(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:mercerie,couturier',
    ]);

    // 📦 Avatar par défaut selon le rôle
    $defaultAvatar = $data['role'] === 'mercerie'
        ? 'images/avatars/mercerie.png'
        : 'images/avatars/couturier.png';

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role' => $data['role'],
        'avatar' => $defaultAvatar, // <--- ajout ici
    ]);

    // auth()->login($user);

    return redirect()->route('login.form')->with('success', 'Inscription réussie ! Connectez-vous pour continuer.');
}


    // Afficher le formulaire de connexion
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Connexion via Blade
    public function loginWeb(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($credentials)) {
            return back()->withErrors(['email' => 'Identifiants incorrects'])->withInput();
        }

        $request->session()->regenerate();

        return redirect()->route('supplies.index')->with('success', 'Connexion réussie !');
    }

    // Déconnexion via Blade
    public function logoutWeb(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->with('success', 'Déconnexion réussie !');
    }

}
