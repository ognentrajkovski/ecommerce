<?php

namespace App\Domain\Cart\Services;

use App\Domain\Cart\Models\Cart;
use RuntimeException;

class CartStockValidationService
{
    /**
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Cart\Models\CartItem> $cartItems
     * @return array<string, string>
     */
    public function validateItems($cartItems): array
    {
        $errors = [];

        foreach ($cartItems as $item) {
            $product = $item->product;

            if ($product === null) {
                $errors[$item->id] = "One of the items in your cart is no longer available.";
                continue;
            }

            if ($item->quantity > $product->stock) {
                $errors[$item->id] = "Insufficient stock for {$product->name}. Only {$product->stock} available.";
            }
        }

        return $errors;
    }

    /**
     * Helper to validate a specific quantity against a product.
     */
    public function validateForAdd(\App\Domain\ProductCatalog\Models\Product $product, int $quantity): void
    {
        if ($quantity > $product->stock) {
            throw new RuntimeException("Insufficient stock for product: {$product->name}");
        }
    }
}
