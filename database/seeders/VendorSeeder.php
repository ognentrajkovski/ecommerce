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
        User::query()
            ->where('role', UserRole::Vendor->value)
            ->get()
            ->each(function (User $user): void {
                Vendor::factory()->for($user)->create();
            });
    }
}
