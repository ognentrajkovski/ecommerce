<?php

namespace App\Domain\OrderManagement\Services;

use App\Domain\OrderManagement\Enums\PaymentMethod;

class PaymentSimulatorService
{
    public function process(float $total, PaymentMethod $method): bool
    {
        return $total <= 999;
    }
}
