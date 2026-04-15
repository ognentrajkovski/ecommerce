<?php

use App\Domain\Cart\Models\Cart;
use App\Domain\OrderManagement\Enums\PaymentMethod;
use App\Domain\OrderManagement\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $paymentMethod = 'credit_card';

    public function getCartProperty()
    {
        $user = Auth::user();

        if ($user === null) {
            return null;
        }

        return Cart::query()
            ->where('user_id', $user->id)
            ->with(['items.product'])
            ->first();
    }

    public function getGrandTotalProperty(): float
    {
        $cart = $this->cart;

        if ($cart === null) {
            return 0.0;
        }

        return (float) $cart->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    public function placeOrder(CheckoutService $checkoutService)
    {
        $method = PaymentMethod::tryFrom($this->paymentMethod);

        if ($method === null) {
            $this->dispatch('notify', message: 'Invalid payment method selected.');
            return;
        }

        try {
            $checkoutService->checkout(Auth::user(), $method);
            return redirect()->route('buyer.orders.index');
        } catch (\RuntimeException $e) {
            $this->dispatch('notify', message: $e->getMessage());
        }
    }
};
?>

<div class="mx-auto max-w-5xl space-y-8">
    <h1 class="text-3xl font-bold">Checkout</h1>
    
    @if($this->cart === null || $this->cart->items->isEmpty())
        <div class="rounded-lg border bg-white p-12 text-center shadow-sm">
            <h2 class="text-lg font-medium text-gray-900">Your cart is empty</h2>
            <a href="{{ route('market.index') }}" class="inline-block mt-4 rounded-md bg-black px-4 py-2 text-sm text-white hover:bg-gray-800">
                Go shopping
            </a>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="space-y-6">
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold">Order Summary</h2>
                    <ul class="divide-y divide-gray-200">
                        @foreach($this->cart->items as $item)
                            <li class="flex justify-between py-4" wire:key="checkout-item-{{ $item->id }}">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-medium text-gray-900">${{ number_format($item->quantity * $item->unit_price, 2) }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 flex justify-between border-t border-gray-200 pt-4 text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>${{ number_format($this->grandTotal, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="rounded-lg border bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-xl font-semibold">Payment Method</h2>
                    
                    <div class="space-y-4">
                        <label class="flex cursor-pointer items-center space-x-3">
                            <input type="radio" name="paymentMethod" wire:model="paymentMethod" value="credit_card" class="h-4 w-4 border-gray-300 text-black focus:ring-black">
                            <span class="font-medium text-gray-900">Credit Card</span>
                        </label>
                        <label class="flex cursor-pointer items-center space-x-3">
                            <input type="radio" name="paymentMethod" wire:model="paymentMethod" value="wallet" class="h-4 w-4 border-gray-300 text-black focus:ring-black">
                            <span class="font-medium text-gray-900">Digital Wallet</span>
                        </label>
                    </div>

                    <button type="button" wire:click="placeOrder" class="mt-8 w-full rounded-md bg-black px-4 py-3 text-lg font-medium text-white transition-colors hover:bg-gray-800">
                        Place Order
                    </button>
                    
                    <p class="mt-4 text-center text-xs text-gray-500">Totals exceeding $999.00 will automatically be declined by simulation parameters.</p>
                </div>
            </div>
        </div>
    @endif
</div>
