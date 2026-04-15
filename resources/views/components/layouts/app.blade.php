<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Ecommerce') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <nav class="border-b bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="/" class="text-lg font-semibold">Ecommerce</a>

            <div class="flex items-center gap-4">
                @auth
                    @if(auth()->user()->role === \App\Domain\IdentityAndAccess\Enums\UserRole::Buyer)
                        <a href="/cart" class="text-sm font-medium hover:text-gray-600">Cart</a>
                        <a href="/orders" class="text-sm font-medium hover:text-gray-600">My Orders</a>
                    @elseif(auth()->user()->role === \App\Domain\IdentityAndAccess\Enums\UserRole::Vendor)
                        <a href="/vendor/products" class="text-sm font-medium hover:text-gray-600">My Products</a>
                        <a href="/vendor/orders" class="text-sm font-medium hover:text-gray-600">Manage Orders</a>
                    @endif

                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="text-sm font-medium hover:text-gray-600">Logout</button>
                    </form>
                @else
                    <a href="/login" class="text-sm font-medium hover:text-gray-600">Login</a>
                    <a href="/register" class="text-sm font-medium hover:text-gray-600">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
