<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


Route::get('/', [PurchaseController::class, 'welcome'])->name('purchase.welcome');
Route::resource('purchase', PurchaseController::class);
Route::resource('cart', CartController::class );
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::get('/success', [CartController::class, 'succes'])->name('cart.success');
Route::get('/items/{item}/source', [ItemController::class, 'source'])->name('items.source');
Route::post('/webhook/mollie', function (Request $request) {
    // Optional: log the webhook for debugging
    Log::info('Mollie webhook received', $request->all());
    // Here you would fetch payment details from Mollie if needed:
    // $payment = Mollie::api()->payments()->get($request->id);
    return response()->json(['status' => 'received']);
})->name('webhook.mollie');
Route::post('/mollie/payment', [CartController::class, 'store'])->name('mollie.payment');
/*Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::resource('items', ItemController::class);
});
