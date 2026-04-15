<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'market.index')->name('market.index');

Volt::route('/products/{product}', 'market.show')->name('products.show');

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
    Volt::route('/vendor/products', 'vendor.products.index')->name('vendor.products.index');
    Volt::route('/vendor/products/create', 'vendor.products.create')->name('vendor.products.create');
    Volt::route('/vendor/products/{product}/edit', 'vendor.products.edit')->name('vendor.products.edit');
    Volt::route('/vendor/orders', 'vendor.orders.index')->name('vendor.orders.index');
});

Route::middleware(['auth', 'role:buyer,vendor'])->group(function (): void {
    Volt::route('/cart', 'cart.index')->name('cart.index');
    Volt::route('/checkout', 'checkout.index')->name('checkout.index');
    Volt::route('/orders', 'buyer.orders.index')->name('buyer.orders.index');
    Volt::route('/orders/{order}', 'buyer.orders.show')->name('buyer.orders.show');
});
