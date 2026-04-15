<?php

use App\Domain\ProductCatalog\Actions\UpdateProductAction;
use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Product $product;

    public string $name = '';
    public string $description = '';
    public string $price = '';
    public int $stock_quantity = 0;
    public ?string $image_url = null;

    public function mount(Product $product): void
    {
        $vendor = Auth::user()?->vendor;

        if ($vendor === null || $product->vendor_id !== $vendor->id) {
            abort(403);
        }

        $this->product = $product;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = (string) $product->price;
        $this->stock_quantity = $product->stock;
        $this->image_url = $product->image_url;
    }

    public function save(UpdateProductAction $updateProductAction): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $updateProductAction->execute($this->product, [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => number_format((float) $validated['price'], 2, '.', ''),
            'stock' => (int) $validated['stock_quantity'],
            'image_url' => $validated['image_url'] ?: null,
        ]);

        $this->redirectRoute('vendor.products.index');
    }
};
?>

<div class="mx-auto max-w-2xl rounded-lg border bg-white p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Edit Product</h1>
        <a href="{{ route('vendor.products.index') }}" class="text-sm text-gray-600 hover:text-black">Cancel</a>
    </div>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Name</label>
            <input id="name" type="text" wire:model="name" class="w-full rounded-md border px-3 py-2 focus:border-black focus:ring-black" />
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="mb-1 block text-sm font-medium">Description</label>
            <textarea id="description" rows="4" wire:model="description" class="w-full rounded-md border px-3 py-2 focus:border-black focus:ring-black"></textarea>
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="price" class="mb-1 block text-sm font-medium">Price</label>
                <input id="price" type="number" min="0" step="0.01" wire:model="price" class="w-full rounded-md border px-3 py-2 focus:border-black focus:ring-black" />
                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="stock_quantity" class="mb-1 block text-sm font-medium">Stock Quantity</label>
                <input id="stock_quantity" type="number" min="0" wire:model="stock_quantity" class="w-full rounded-md border px-3 py-2 focus:border-black focus:ring-black" />
                @error('stock_quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="image_url" class="mb-1 block text-sm font-medium">Image URL</label>
            <input id="image_url" type="url" wire:model="image_url" class="w-full rounded-md border px-3 py-2 focus:border-black focus:ring-black" />
            @error('image_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded-md bg-black px-4 py-3 font-medium text-white hover:bg-gray-800 transition-colors">Save Changes</button>
    </form>
</div>
