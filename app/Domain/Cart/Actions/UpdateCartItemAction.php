<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\Models\CartItem;
use App\Domain\Cart\Services\CartStockValidationService;

class UpdateCartItemAction
{
    public function __construct(
        private readonly CartStockValidationService $validationService
    ) {
    }

    public function execute(CartItem $cartItem, int $newQuantity): CartItem
    {
        // Validates stock and throws RuntimeException if insufficient
        $this->validationService->validateForAdd($cartItem->product, $newQuantity);

        $cartItem->update([
            'quantity' => $newQuantity,
        ]);

        return $cartItem;
    }
}
