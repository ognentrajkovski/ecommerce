<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\Models\CartItem;
use App\Domain\IdentityAndAccess\Models\User;

class RemoveFromCartAction
{
    public function execute(User $user, string $cartItemId): void
    {
        $cart = $user->cart;

        if ($cart === null) {
            return;
        }

        CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->delete();
    }
}
