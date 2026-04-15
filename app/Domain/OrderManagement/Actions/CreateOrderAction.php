<?php

namespace App\Domain\OrderManagement\Actions;

use App\Domain\OrderManagement\DTOs\CreateOrderDTO;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Models\Order;

class CreateOrderAction
{
    public function execute(CreateOrderDTO $dto): Order
    {
        $totalPrice = 0.0;
        foreach ($dto->items as $item) {
            $totalPrice += $item['quantity'] * $item['unit_price'];
        }

        $order = Order::create([
            'user_id' => $dto->user_id,
            'status' => OrderStatus::Pending->value,
            'total_price' => $totalPrice,
            'payment_method' => $dto->payment_method->value,
        ]);

        foreach ($dto->items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'vendor_id' => $item['vendor_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return $order->refresh();
    }
}
