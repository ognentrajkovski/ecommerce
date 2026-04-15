<?php

namespace App\Domain\OrderManagement\Services;

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Services\CartStockValidationService;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\OrderManagement\Actions\CreateOrderAction;
use App\Domain\OrderManagement\DTOs\CreateOrderDTO;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Enums\PaymentMethod;
use App\Domain\OrderManagement\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private readonly CartStockValidationService $validationService,
        private readonly CreateOrderAction $createOrderAction,
        private readonly PaymentSimulatorService $paymentSimulatorService
    ) {
    }

    public function checkout(User $user, PaymentMethod $method): Order
    {
        return DB::transaction(function () use ($user, $method) {
            /** @var Cart|null $cart */
            $cart = $user->cart;

            if ($cart === null || $cart->items->isEmpty()) {
                throw new RuntimeException("Cart is empty");
            }

            $cartItems = $cart->items()->with('product')->get();

            // 1. Validate stock
            $errors = $this->validationService->validateItems($cartItems);
            
            if (!empty($errors)) {
                throw new RuntimeException(implode(', ', $errors));
            }

            // 2. Build CreateOrderDTO from cart contents
            $itemsDto = [];
            foreach ($cartItems as $item) {
                $itemsDto[] = [
                    'product_id' => $item->product_id,
                    'vendor_id' => $item->product->vendor_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            }

            $dto = new CreateOrderDTO(
                user_id: $user->id,
                payment_method: $method,
                items: $itemsDto
            );

            // 3. Call CreateOrderAction
            $order = $this->createOrderAction->execute($dto);

            // 4. Call PaymentSimulatorService
            $paymentSuccess = $this->paymentSimulatorService->process((float) $order->total_price, $method);

            if (!$paymentSuccess) {
                throw new RuntimeException("Payment declined");
            }

            // 5. Decrement stock for each product
            foreach ($cartItems as $item) {
                $product = $item->product;
                $product->decrement('stock', $item->quantity);
            }

            // 6. Clear the cart
            $cart->items()->delete();

            // 7. Set order status to paid
            $order->update(['status' => OrderStatus::Paid->value]);

            return $order->refresh();
        });
    }
}
