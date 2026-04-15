<?php

namespace App\Domain\ProductCatalog\Actions;

use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Support\Str;

class UpdateProductAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Product $product, array $data): Product
    {
        $payload = [
            'name' => $data['name'] ?? $product->name,
            'description' => $data['description'] ?? $product->description,
            'price' => $data['price'] ?? $product->price,
            'stock' => $data['stock_quantity'] ?? $data['stock'] ?? $product->stock,
            'image_url' => $data['image_url'] ?? $product->image_url,
            'is_active' => $data['is_active'] ?? $product->is_active,
        ];

        if (($data['name'] ?? null) !== null && (string) $data['name'] !== $product->name) {
            $payload['slug'] = Str::slug((string) $data['name']).'-'.Str::lower(Str::random(6));
        }

        $product->update($payload);

        return $product->refresh();
    }
}
