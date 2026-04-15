<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function (): \Illuminate\Contracts\View\View {
    return view('welcome');
})->name('market.index');

Route::get('/products/{product}', function (string $product): \Illuminate\Contracts\View\View {
    return view('welcome', ['product' => $product]);
})->name('products.show');

Route::middleware('guest')->group(function (): void {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/register', 'auth.register')->name('register');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', function (Request $request): \Illuminate\Http\RedirectResponse {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

Route::middleware(['auth', 'role:vendor'])->group(function (): void {
    Route::view('/vendor/products', 'welcome')->name('vendor.products.index');
    Route::view('/vendor/orders', 'welcome')->name('vendor.orders.index');
});

Route::middleware(['auth', 'role:buyer'])->group(function (): void {
    Route::view('/cart', 'welcome')->name('cart.index');
    Route::view('/checkout', 'welcome')->name('checkout.index');
    Route::view('/orders', 'welcome')->name('buyer.orders.index');
});
