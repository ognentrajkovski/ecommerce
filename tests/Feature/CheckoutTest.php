<?php

namespace Tests\Feature;

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Models\CartItem;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Enums\PaymentMethod;
use App\Domain\OrderManagement\Services\CheckoutService;
use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private CheckoutService $checkoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkoutService = $this->app->make(CheckoutService::class);
    }

    public function test_successful_checkout_decrements_stock_clears_cart_sets_status_paid(): void
    {
        $user = User::factory()->create(['role' => UserRole::Buyer->value]);
        $cart = Cart::create(['user_id' => $user->id]);
        
        $product = Product::factory()->create(['stock' => 10, 'price' => 50.00]);
        
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 50.00
        ]);

        $order = $this->checkoutService->checkout($user, PaymentMethod::CreditCard);

        $this->assertEquals(OrderStatus::Paid, $order->status);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'total_price' => 100.00]);
        
        // Stock decremented
        $this->assertEquals(8, $product->fresh()->stock);

        // Cart is cleared
        $this->assertTrue($cart->fresh()->items->isEmpty());
    }

    public function test_payment_failure_does_not_create_order_leaves_cart_intact(): void
    {
        $user = User::factory()->create(['role' => UserRole::Buyer->value]);
        $cart = Cart::create(['user_id' => $user->id]);
        
        // Total over $999 to trigger failure
        $product = Product::factory()->create(['stock' => 5, 'price' => 1000.00]);
        
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000.00
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Payment declined');

        try {
            $this->checkoutService->checkout($user, PaymentMethod::CreditCard);
        } finally {
            $this->assertDatabaseCount('orders', 0);
            $this->assertEquals(5, $product->fresh()->stock);
            $this->assertFalse($cart->fresh()->items->isEmpty());
        }
    }

    public function test_stock_failure_throws_exception_leaves_cart_intact(): void
    {
        $user = User::factory()->create(['role' => UserRole::Buyer->value]);
        $cart = Cart::create(['user_id' => $user->id]);
        
        // Stock only 1, trying to buy 2
        $product = Product::factory()->create(['stock' => 1, 'price' => 100.00]);
        
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100.00
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Insufficient stock');

        try {
            $this->checkoutService->checkout($user, PaymentMethod::CreditCard);
        } finally {
            $this->assertDatabaseCount('orders', 0);
            $this->assertEquals(1, $product->fresh()->stock);
            $this->assertFalse($cart->fresh()->items->isEmpty());
        }
    }
}
