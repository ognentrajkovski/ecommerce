<?php

namespace App\Domain\IdentityAndAccess\Actions;

use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterUserAction
{
    public function execute(string $name, string $email, string $password, string $role): User
    {
        return DB::transaction(function () use ($name, $email, $password, $role): User {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role,
            ]);

            if ($role === UserRole::Vendor->value) {
                Vendor::query()->create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
                    'is_active' => true,
                ]);
            }

            return $user;
        });
    }
}
