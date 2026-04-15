<?php

namespace App\Domain\ProductCatalog\Services;

use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MarketplaceSearchService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters): LengthAwarePaginator
    {
        return Product::query()
            ->with('vendor')
            ->active()
            ->when(
                filled($filters['keyword'] ?? null),
                fn ($query) => $query->where('name', 'like', '%'.trim((string) $filters['keyword']).'%')
            )
            ->when(
                filled($filters['vendor_id'] ?? null),
                fn ($query) => $query->where('vendor_id', (string) $filters['vendor_id'])
            )
            ->when(
                filled($filters['min_price'] ?? null),
                fn ($query) => $query->where('price', '>=', (float) $filters['min_price'])
            )
            ->when(
                filled($filters['max_price'] ?? null),
                fn ($query) => $query->where('price', '<=', (float) $filters['max_price'])
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }
}
