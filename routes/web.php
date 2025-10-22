<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\NotificationController;
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
        Route::get('/supplies/search', [SupplyController::class, 'search'])->name('supplies.search');

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
        Route::post('/supplies', [MerchantSupplyController::class, 'store'])->name('merchant.supplies.store');
        Route::get('/supplies/{id}/edit', [MerchantSupplyController::class, 'edit'])->name('merchant.supplies.edit');
        Route::put('/supplies/{id}', [MerchantSupplyController::class, 'update'])->name('merchant.supplies.update');
        Route::get('/merceries/profile/edit', [MerchantController::class, 'edit'])->name('merceries.profile.edit');
        Route::put('/merceries/profile/update', [MerchantController::class, 'updateProfile'])->name('merceries.profile.update');

        Route::delete('/supplies/{id}', [MerchantSupplyController::class, 'destroy'])->name('merchant.supplies.destroy');

    });

    Route::prefix('merchant')->name('merchant.')->group(function() {
        Route::post('/orders/{id}/accept', [OrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/{id}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    });


    Route::middleware(['auth'])->group(function () {
        Route::get('/merchant/supplies/create', [MerchantSupplyController::class, 'create'])
            ->name('merchant.supplies.create');
        Route::get('/merchant/supplies/search', [MerchantSupplyController::class, 'searchSupplies'])
            ->name('merchant.supplies.search');
    });

    // Couturier
    Route::prefix('couturier')->middleware('auth')->group(function () {
        Route::get('/merceries', [MerchantController::class, 'index'])->name('merceries.index');
        Route::get('/merceries/{id}', [MerchantController::class, 'show'])->name('merceries.show');
        Route::post('/merceries/{id}/order', [OrderController::class, 'storeFromMerchant'])->name('merceries.order');
        Route::get('/api/merceries/search', [MerchantController::class, 'searchAjax'])->name('api.merceries.search');
    });

    // Recherche AJAX (live)
    Route::get('/api/fournitures/search', [SupplyController::class, 'searchAjax'])->middleware('auth')->name('api.supplies.search');

    // Routes pour les notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');
    });


});
