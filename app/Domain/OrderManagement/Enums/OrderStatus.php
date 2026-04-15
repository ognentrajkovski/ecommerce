<?php

namespace App\Domain\OrderManagement\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function canTransitionTo(OrderStatus $status): bool
    {
        return match ($this) {
            self::Pending => $status === self::Paid,
            self::Paid => $status === self::Shipped,
            self::Shipped => $status === self::Delivered,
            self::Delivered => false,
        };
    }
}
