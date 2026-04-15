<?php

namespace Tests\Unit;

use App\Domain\OrderManagement\Enums\PaymentMethod;
use App\Domain\OrderManagement\Services\PaymentSimulatorService;
use PHPUnit\Framework\TestCase;

class PaymentSimulatorServiceTest extends TestCase
{
    public function test_orders_under_or_equal_to_limit_return_true(): void
    {
        $service = new PaymentSimulatorService();

        $this->assertTrue($service->process(100.00, PaymentMethod::CreditCard));
        $this->assertTrue($service->process(999.00, PaymentMethod::Wallet));
    }

    public function test_orders_over_limit_return_false(): void
    {
        $service = new PaymentSimulatorService();

        $this->assertFalse($service->process(999.01, PaymentMethod::CreditCard));
        $this->assertFalse($service->process(5000.00, PaymentMethod::Wallet));
    }
}
