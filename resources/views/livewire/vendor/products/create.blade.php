<?php

use App\Domain\ProductCatalog\Actions\CreateProductAction;
use App\Domain\ProductCatalog\DTOs\CreateProductDTO;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $name = '';

    public string $description = '';

    public string $price = '';

    public int $stock_quantity = 0;

    public ?string $image_url = null;

    public function save(CreateProductAction $createProductAction): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $vendor = Auth::user()?->vendor;

        if ($vendor === null) {
            abort(403);
        }

        $createProductAction->execute(new CreateProductDTO(
            name: $validated['name'],
            description: $validated['description'],
            price: number_format((float) $validated['price'], 2, '.', ''),
            stock_quantity: (int) $validated['stock_quantity'],
            image_url: $validated['image_url'] ?: null,
            vendor_id: $vendor->id,
        ));

        $this->redirectRoute('vendor.products.index');
    }
};
?>

<div class="mx-auto max-w-2xl rounded-lg border bg-white p-6">
    <h1 class="mb-6 text-2xl font-semibold">Create Product</h1>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Name</label>
            <input id="name" type="text" wire:model="name" class="w-full rounded-md border px-3 py-2" />
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="mb-1 block text-sm font-medium">Description</label>
            <textarea id="description" rows="4" wire:model="description" class="w-full rounded-md border px-3 py-2"></textarea>
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="price" class="mb-1 block text-sm font-medium">Price</label>
                <input id="price" type="number" min="0" step="0.01" wire:model="price" class="w-full rounded-md border px-3 py-2" />
                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="stock_quantity" class="mb-1 block text-sm font-medium">Stock Quantity</label>
                <input id="stock_quantity" type="number" min="0" wire:model="stock_quantity" class="w-full rounded-md border px-3 py-2" />
                @error('stock_quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="image_url" class="mb-1 block text-sm font-medium">Image URL</label>
            <input id="image_url" type="url" wire:model="image_url" class="w-full rounded-md border px-3 py-2" />
            @error('image_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="rounded-md bg-black px-4 py-2 text-white">Create Product</button>
    </form>
</div>
