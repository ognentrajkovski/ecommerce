<?php

use App\Domain\ProductCatalog\Models\Vendor;
use App\Domain\ProductCatalog\Services\MarketplaceSearchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\Cart\Actions\AddToCartAction;
use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public string $keyword = '';

    public string $vendor_id = '';

    public string $min_price = '';

    public string $max_price = '';

    public array $addedToCartMap = [];

    public function updatingKeyword(): void
    {
        $this->resetPage();
    }

    public function updatingVendorId(): void
    {
        $this->resetPage();
    }

    public function updatingMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatingMaxPrice(): void
    {
        $this->resetPage();
    }

    public function getProductsProperty(MarketplaceSearchService $searchService): LengthAwarePaginator
    {
        return $searchService->search([
            'keyword' => $this->keyword,
            'vendor_id' => $this->vendor_id,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
        ]);
    }

    public function getVendorsProperty(): Collection
    {
        return Vendor::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function addToCart(string $productId, AddToCartAction $action): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }

        $product = Product::find($productId);
        if (!$product) return;

        try {
            $action->execute(Auth::user(), $product, 1);
            $this->addedToCartMap[$productId] = true;
            $this->dispatch('notify', message: 'Added to cart!');
        } catch (\RuntimeException $e) {
            $this->dispatch('notify', message: $e->getMessage());
        }
    }
};
?>

<div class="space-y-6">
    <div class="rounded-lg border bg-white p-4">
        <div class="grid gap-4 md:grid-cols-4">
            <input
                type="text"
                wire:model.live.debounce.300ms="keyword"
                placeholder="Search products..."
                class="w-full rounded-md border px-3 py-2"
            />

            <select wire:model.live="vendor_id" class="w-full rounded-md border px-3 py-2">
                <option value="">All vendors</option>
                @foreach($this->vendors as $vendor)
                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                @endforeach
            </select>

            <input
                type="number"
                min="0"
                step="10"
                wire:model.live.debounce.300ms="min_price"
                placeholder="Min price"
                class="w-full rounded-md border px-3 py-2"
            />

            <input
                type="number"
                min="0"
                step="10"
                wire:model.live.debounce.300ms="max_price"
                placeholder="Max price"
                class="w-full rounded-md border px-3 py-2"
            />
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($this->products as $product)
            <div class="rounded-lg border bg-white p-4 shadow-sm" wire:key="product-{{ $product->id }}">
                <a href="{{ route('products.show', $product) }}">
                    <img
                        src="{{ $product->image_url ?: 'https://placehold.co/600x400?text=Product' }}"
                        alt="{{ $product->name }}"
                        class="mb-4 h-40 w-full rounded-md object-cover"
                    />
                </a>

                <h2 class="text-lg font-semibold">{{ $product->name }}</h2>
                <p class="text-sm text-gray-600">{{ $product->vendor?->name }}</p>
                <p class="mt-2 text-sm font-medium">${{ number_format((float) $product->price, 2) }}</p>
                <p class="mt-1 text-sm text-gray-600">Stock: {{ $product->stock }}</p>

                <button
                    type="button"
                    wire:click="addToCart('{{ $product->id }}')"
                    @disabled($product->stock <= 0)
                    class="mt-4 w-full rounded-md border border-black px-3 py-2 text-sm font-medium transition-colors disabled:cursor-not-allowed disabled:border-transparent disabled:bg-gray-300 disabled:text-white {{ !empty($this->addedToCartMap[$product->id]) ? 'bg-white text-black' : 'bg-black text-white' }}"
                >
                    {{ !empty($this->addedToCartMap[$product->id]) ? 'Added to Cart' : 'Add to Cart' }}
                </button>
            </div>
        @empty
            <p class="text-sm text-gray-600">No products found.</p>
        @endforelse
    </div>

    <div>
        {{ $this->products->links() }}
    </div>
</div>
