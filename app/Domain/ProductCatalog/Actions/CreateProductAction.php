<?php

namespace App\Domain\ProductCatalog\Actions;

use App\Domain\ProductCatalog\DTOs\CreateProductDTO;
use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Support\Str;

class CreateProductAction
{
    public function execute(CreateProductDTO $dto): Product
    {
        return Product::query()->create([
            'vendor_id' => $dto->vendor_id,
            'name' => $dto->name,
            'description' => $dto->description,
            'image_url' => $dto->image_url,
            'slug' => Str::slug($dto->name).'-'.Str::lower(Str::random(6)),
            'price' => $dto->price,
            'stock' => $dto->stock_quantity,
            'is_active' => true,
        ]);
    }
}
