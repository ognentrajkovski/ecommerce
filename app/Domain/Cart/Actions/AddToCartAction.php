<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Models\CartItem;
use App\Domain\Cart\Services\CartStockValidationService;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\ProductCatalog\Models\Product;

class AddToCartAction
{
    public function __construct(
        private readonly CartStockValidationService $validationService
    ) {
    }

    public function execute(User $user, Product $product, int $quantity): CartItem
    {
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;

        // Validates stock and throws RuntimeException if insufficient
        $this->validationService->validateForAdd($product, $newQuantity);

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQuantity,
                'unit_price' => $product->price,
            ]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
            ]);
        }

        return $cartItem;
    }
}
