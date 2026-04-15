<?php

namespace Database\Seeders;

use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $brands = ['Nike', 'Adidas', 'Jordan', 'NewBalance'];

        User::query()
            ->where('role', UserRole::Vendor->value)
            ->get()
            ->each(function (User $user) use (&$brands): void {
                $brand = array_shift($brands);
                if ($brand) {
                    Vendor::factory()->for($user)->create([
                        'name' => $brand,
                        'slug' => \Illuminate\Support\Str::slug($brand),
                    ]);
                } else {
                    Vendor::factory()->for($user)->create();
                }
            });
    }
}
