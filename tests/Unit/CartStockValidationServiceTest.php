<?php

namespace Tests\Unit;

use App\Domain\Cart\Models\CartItem;
use App\Domain\Cart\Services\CartStockValidationService;
use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class CartStockValidationServiceTest extends TestCase
{
    public function test_no_errors_when_stock_is_sufficient(): void
    {
        $service = new CartStockValidationService();

        $product = new Product(['name' => 'Sneakers', 'stock' => 10]);
        $item = new CartItem(['quantity' => 2]);
        $item->setRelation('product', $product);

        $cartItems = new Collection([$item]);

        $errors = $service->validateItems($cartItems);

        $this->assertEmpty($errors);
    }

    public function test_returns_errors_when_quantity_exceeds_stock(): void
    {
        $service = new CartStockValidationService();

        $product = new Product(['name' => 'Sneakers', 'stock' => 5]);
        $item = new CartItem(['quantity' => 10]);
        $item->id = 'test-id';
        $item->setRelation('product', $product);

        $cartItems = new Collection([$item]);

        $errors = $service->validateItems($cartItems);

        $this->assertArrayHasKey('test-id', $errors);
        $this->assertStringContainsString('Insufficient stock', $errors['test-id']);
    }

    public function test_returns_error_when_product_is_soft_deleted(): void
    {
        $service = new CartStockValidationService();

        $item = new CartItem(['quantity' => 2]);
        $item->id = 'test-id-2';
        $item->setRelation('product', null);

        $cartItems = new Collection([$item]);

        $errors = $service->validateItems($cartItems);

        $this->assertArrayHasKey('test-id-2', $errors);
        $this->assertStringContainsString('no longer available', $errors['test-id-2']);
    }
}
