<?php

use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public function getProductsProperty()
    {
        $vendor = Auth::user()?->vendor;

        if ($vendor === null) {
            return collect();
        }

        return Product::query()
            ->forVendor($vendor->id)
            ->latest()
            ->get();
    }

    public function deleteProduct(string $productId): void
    {
        $vendor = Auth::user()?->vendor;

        if ($vendor === null) {
            abort(403);
        }

        $product = Product::query()
            ->forVendor($vendor->id)
            ->findOrFail($productId);

        $product->delete();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">My Products</h1>
        <a href="{{ route('vendor.products.create') }}" class="rounded-md bg-black px-4 py-2 text-sm text-white">
            Create Product
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border bg-white">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-600">Stock</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($this->products as $product)
                    <tr>
                        <td class="px-4 py-3">{{ $product->name }}</td>
                        <td class="px-4 py-3">${{ number_format((float) $product->price, 2) }}</td>
                        <td class="px-4 py-3">{{ $product->stock }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" class="mr-2 rounded border px-3 py-1 text-sm text-gray-700">Edit</button>
                            <button
                                type="button"
                                wire:click="deleteProduct('{{ $product->id }}')"
                                class="rounded border border-red-300 px-3 py-1 text-sm text-red-700"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-600">No products yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
