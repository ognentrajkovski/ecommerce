<?php

namespace App\Domain\OrderManagement\Factories;

use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalPrice = fake()->randomFloat(2, 10, 1999);

        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::Pending,
            'total_price' => number_format($totalPrice, 2, '.', ''),
            'payment_method' => fake()->randomElement(['card', 'cash_on_delivery']),
        ];
    }
}
