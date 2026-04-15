<?php

namespace Database\Seeders;

use App\Domain\IdentityAndAccess\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->create();
        User::factory()->vendor()->count(4)->create();
    }
}
