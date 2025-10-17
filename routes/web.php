<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MerchantSupplyController;
use App\Http\Controllers\PriceComparisonController;

/*
|--------------------------------------------------------------------------
| Routes publiques (accessible sans login)
|--------------------------------------------------------------------------
*/

// Page d'accueil
// Route::get('/', function () {
//     return view('welcome');
// });

// Inscription
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'registerWeb'])->name('register.submit');

// Connexion
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.submit');

// Liste des fournitures (vue publique)
Route::get('/', [SupplyController::class, 'index'])->name('supplies.index');


/*
|--------------------------------------------------------------------------
| Routes protégées (auth middleware)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

    // Couturier
    Route::prefix('couturier')->group(function() {
        // Étape 2 : Sélection des fournitures et quantité
        Route::get('/fournitures/selection', [SupplyController::class, 'selectionForm'])->name('supplies.selection');

        // Étape 3 : Comparaison des merceries
        Route::post('/merceries/comparer', [PriceComparisonController::class, 'compare'])->name('merceries.compare');

        // Étape 4 : Création de la commande
        Route::post('/commande/creer', [OrderController::class, 'storeWeb'])->name('orders.store');

        // Voir ses commandes
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/merceries/{id}/preview', [OrderController::class, 'preview'])->name('merceries.preview');
        Route::post('/mercerie/{mercerieId}/commande', [OrderController::class, 'storeFromMerchant'])->name('orders.storeFromMerchant');
    });

    // Mercerie
    Route::prefix('merchant')->group(function() {
        Route::get('/supplies', [MerchantSupplyController::class, 'index'])->name('merchant.supplies.index');
        Route::get('/supplies/create', [MerchantSupplyController::class, 'create'])->name('merchant.supplies.create');
        Route::post('/supplies', [MerchantSupplyController::class, 'store'])->name('merchant.supplies.store');
        Route::get('/supplies/{id}/edit', [MerchantSupplyController::class, 'edit'])->name('merchant.supplies.edit');
        Route::put('/supplies/{id}', [MerchantSupplyController::class, 'update'])->name('merchant.supplies.update');
        Route::delete('/supplies/{id}', [MerchantSupplyController::class, 'destroy'])->name('merchant.supplies.destroy');
        // Gestion des commandes reçues par la mercerie
        Route::post('/orders/{id}/accept', [OrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/{id}/reject', [OrderController::class, 'reject'])->name('orders.reject');

    });

    // Couturier
    Route::prefix('couturier')->middleware('auth')->group(function () {
        Route::get('/merceries', [MerchantController::class, 'index'])->name('merceries.index');
        Route::get('/merceries/{id}', [MerchantController::class, 'show'])->name('merceries.show');
        Route::post('/merceries/{id}/order', [OrderController::class, 'storeFromMerchant'])->name('merceries.order');
    });



});
