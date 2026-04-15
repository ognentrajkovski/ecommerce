<?php

namespace Database\Seeders;

use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $nikeShoes = ['Air Max 90', 'Air Force 1', 'Dunk Low', 'Blazer Mid', 'React Infinity', 'ZoomX Vaporfly'];
        $adidasShoes = ['Yeezy Boost 350', 'Ultraboost', 'Stan Smith', 'Superstar', 'NMD R1', 'Samba'];
        $jordanShoes = ['Air Jordan 1 High', 'Air Jordan 4 Retro', 'Air Jordan 11', 'Air Jordan 3', 'Jordan 5 Retro'];
        $nbShoes = ['550', '2002R', '990v5', '327', '9060', '574 Core'];
        $genericShoes = ['Runner X', 'Classic Low', 'Sport High Top'];

        Vendor::query()
            ->get()
            ->each(function (Vendor $vendor) use ($nikeShoes, $adidasShoes, $jordanShoes, $nbShoes, $genericShoes): void {
                $shoes = match(strtolower($vendor->name)) {
                    'nike' => $nikeShoes,
                    'adidas' => $adidasShoes,
                    'jordan' => $jordanShoes,
                    'newbalance' => $nbShoes,
                    default => $genericShoes,
                };
                
                foreach ($shoes as $shoe) {
                    Product::factory()
                        ->for($vendor)
                        ->create([
                            'name' => $vendor->name . ' ' . $shoe,
                            'slug' => \Illuminate\Support\Str::slug($vendor->name . ' ' . $shoe . ' ' . rand(100, 999)),
                            'description' => 'A premium pair of authentic ' . $vendor->name . ' ' . $shoe . ' sneakers.',
                            'image_url' => 'https://placehold.co/400x400?text=' . urlencode($vendor->name . '+' . explode(' ', $shoe)[0]),
                            'price' => fake()->randomFloat(2, 90, 350),
                            'stock' => fake()->numberBetween(10, 50),
                            'is_active' => true,
                        ]);
                }
            });
    }
}
