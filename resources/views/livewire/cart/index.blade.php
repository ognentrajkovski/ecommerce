<?php

use App\Domain\Cart\Actions\RemoveFromCartAction;
use App\Domain\Cart\Actions\UpdateCartItemAction;
use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function getCartProperty()
    {
        $user = Auth::user();

        if ($user === null) {
            return null;
        }

        return Cart::query()
            ->where('user_id', $user->id)
            ->with(['items.product.vendor'])
            ->first();
    }

    public function getGroupedItemsProperty()
    {
        $cart = $this->cart;

        if ($cart === null) {
            return collect();
        }

        return $cart->items->groupBy(function (CartItem $item) {
            return $item->product->vendor->name ?? 'Unknown Vendor';
        });
    }

    public function getGrandTotalProperty(): float
    {
        $cart = $this->cart;

        if ($cart === null) {
            return 0.0;
        }

        return (float) $cart->items->sum(function (CartItem $item) {
            return $item->quantity * $item->unit_price;
        });
    }

    public function incrementQuantity(string $cartItemId, UpdateCartItemAction $updateAction): void
    {
        $cartItem = CartItem::find($cartItemId);

        if ($cartItem === null) {
            return;
        }

        try {
            $updateAction->execute($cartItem, $cartItem->quantity + 1);
        } catch (\RuntimeException $e) {
            $this->dispatch('notify', message: $e->getMessage());
        }
    }

    public function decrementQuantity(string $cartItemId, UpdateCartItemAction $updateAction, RemoveFromCartAction $removeAction): void
    {
        $cartItem = CartItem::find($cartItemId);

        if ($cartItem === null) {
            return;
        }

        if ($cartItem->quantity <= 1) {
            $removeAction->execute(Auth::user(), $cartItemId);
            return;
        }

        try {
            $updateAction->execute($cartItem, $cartItem->quantity - 1);
        } catch (\RuntimeException $e) {
            $this->dispatch('notify', message: $e->getMessage());
        }
    }

    public function removeItem(string $cartItemId, RemoveFromCartAction $removeAction): void
    {
        $removeAction->execute(Auth::user(), $cartItemId);
    }
};
?>

<div class="mx-auto max-w-5xl space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold">Shopping Cart</h1>
    </div>

    @if($this->cart === null || $this->groupedItems->isEmpty())
        <div class="rounded-lg border bg-white p-12 text-center shadow-sm">
            <h2 class="text-lg font-medium text-gray-900">Your cart is empty</h2>
            <p class="mt-2 text-sm text-gray-500">Looks like you haven't added anything yet.</p>
            <a href="{{ route('market.index') }}" class="mt-6 inline-block rounded-md bg-black px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-800">
                Continue Shopping
            </a>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                @foreach($this->groupedItems as $vendorName => $items)
                    @php
                        $vendorSubtotal = $items->sum(fn ($item) => $item->quantity * $item->unit_price);
                    @endphp
                    <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
                        <div class="border-b bg-gray-50 px-6 py-4 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">{{ $vendorName }}</h3>
                            <span class="text-sm font-medium text-gray-600">Subtotal: ${{ number_format((float) $vendorSubtotal, 2) }}</span>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @foreach($items as $item)
                                <li class="flex items-center gap-6 px-6 py-6" wire:key="cart-item-{{ $item->id }}">
                                    <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                        <img src="{{ $item->product->image_url ?: 'https://placehold.co/200x200?text=Product' }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover object-center">
                                    </div>

                                    <div class="flex flex-1 flex-col">
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>
                                                <a href="{{ route('products.show', $item->product) }}">{{ $item->product->name }}</a>
                                            </h3>
                                            <p class="ml-4">${{ number_format((float) $item->unit_price, 2) }}</p>
                                        </div>

                                        <div class="mt-4 flex flex-1 items-end justify-between text-sm">
                                            <div class="flex items-center rounded-md border border-gray-300">
                                                <button type="button" wire:click="decrementQuantity('{{ $item->id }}')" class="px-3 py-1 text-gray-600 hover:bg-gray-50">-</button>
                                                <span class="border-x border-gray-300 px-4 py-1">{{ $item->quantity }}</span>
                                                <button type="button" wire:click="incrementQuantity('{{ $item->id }}')" class="px-3 py-1 text-gray-600 hover:bg-gray-50">+</button>
                                            </div>

                                            <button type="button" wire:click="removeItem('{{ $item->id }}')" class="font-medium text-red-600 hover:text-red-500">Remove</button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <div class="rounded-lg border bg-white p-6 shadow-sm h-fit sticky top-8">
                <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>
                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between border-t pt-4">
                        <span class="text-base font-medium text-gray-900">Grand Total</span>
                        <span class="text-xl font-semibold text-gray-900">${{ number_format($this->grandTotal, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="/checkout" class="flex w-full items-center justify-center rounded-md border border-transparent bg-black px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-gray-800">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
