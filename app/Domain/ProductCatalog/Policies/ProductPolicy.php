<?php

namespace App\Domain\ProductCatalog\Policies;

use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\ProductCatalog\Models\Product;

class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        return $product->vendor !== null && $product->vendor->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $product->vendor !== null && $product->vendor->user_id === $user->id;
    }
}
