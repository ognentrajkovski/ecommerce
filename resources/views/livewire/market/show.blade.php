<?php

use App\Domain\ProductCatalog\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Product $product;

    public int $quantity = 1;

    public function mount(Product $product): void
    {
        $this->product = $product->load('vendor');
    }

    public function incrementQuantity(): void
    {
        if ($this->quantity < max(1, (int) $this->product->stock)) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        $this->dispatch('notify', message: 'Add to cart coming soon.');
    }
};
?>

<div class="grid gap-8 lg:grid-cols-2">
    <img
        src="{{ $product->image_url ?: 'https://placehold.co/900x700?text=Product' }}"
        alt="{{ $product->name }}"
        class="h-full max-h-[480px] w-full rounded-lg border object-cover"
    />

    <div class="space-y-4">
        <h1 class="text-3xl font-semibold">{{ $product->name }}</h1>
        <p class="text-sm text-gray-600">By {{ $product->vendor?->name }}</p>
        <p class="text-xl font-medium">${{ number_format((float) $product->price, 2) }}</p>
        <p class="text-sm text-gray-700">{{ $product->description }}</p>
        <p class="text-sm text-gray-600">Stock: {{ $product->stock }}</p>

        <div class="flex items-center gap-3">
            <button type="button" wire:click="decrementQuantity" class="rounded border px-3 py-1">-</button>
            <span class="min-w-10 text-center">{{ $quantity }}</span>
            <button type="button" wire:click="incrementQuantity" class="rounded border px-3 py-1">+</button>
        </div>

        <button
            type="button"
            wire:click="addToCart"
            @disabled($product->stock <= 0)
            class="rounded-md bg-black px-4 py-2 text-white disabled:cursor-not-allowed disabled:bg-gray-300"
        >
            Add to Cart
        </button>
    </div>
</div>
