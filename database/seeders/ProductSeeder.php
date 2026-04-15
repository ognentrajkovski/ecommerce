<?php

namespace Database\Seeders;

use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Vendor::query()
            ->get()
            ->each(function (Vendor $vendor): void {
                Product::factory()
                    ->count(fake()->numberBetween(10, 12))
                    ->for($vendor)
                    ->state(function (array $attributes): array {
                        return [
                            'is_active' => true,
                            'stock' => fake()->numberBetween(10, 100),
                        ];
                    })
                    ->create();
            });
    }
}
