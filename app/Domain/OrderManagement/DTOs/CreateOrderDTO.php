<?php

namespace App\Domain\OrderManagement\DTOs;

use App\Domain\OrderManagement\Enums\PaymentMethod;

readonly class CreateOrderDTO
{
    public function __construct(
        public string $user_id,
        public PaymentMethod $payment_method,
        /** @var array<int, array{product_id: string, vendor_id: string, quantity: int, unit_price: float}> */
        public array $items
    ) {
    }
}
