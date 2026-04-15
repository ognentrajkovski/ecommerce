<?php

namespace App\Domain\ProductCatalog\Factories;

use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 5, 999);

        return [
            'vendor_id' => Vendor::factory(),
            'name' => Str::title($name),
            'description' => fake()->paragraph(),
            'image_url' => fake()->optional()->imageUrl(),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'price' => number_format($price, 2, '.', ''),
            'stock' => fake()->numberBetween(0, 200),
            'is_active' => true,
        ];
    }
}
