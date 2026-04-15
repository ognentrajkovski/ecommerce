<?php

namespace App\Domain\ProductCatalog\DTOs;

readonly class CreateProductDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public string $price,
        public int $stock_quantity,
        public ?string $image_url,
        public string $vendor_id,
    ) {
    }
}
