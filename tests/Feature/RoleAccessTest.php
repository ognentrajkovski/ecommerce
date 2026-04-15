<?php

namespace Tests\Feature;

use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_cannot_access_vendor_products()
    {
        $buyer = User::factory()->create(['role' => UserRole::Buyer->value]);

        $response = $this->actingAs($buyer)->get('/vendor/products');

        $response->assertStatus(403);
    }

    public function test_vendor_can_access_cart()
    {
        // Based on our updated dynamic routing mapping `role:buyer,vendor`, vendors CAN shop!
        $vendor = User::factory()->create(['role' => UserRole::Vendor->value]);

        $response = $this->actingAs($vendor)->get('/cart');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_cart_and_redirects_to_login()
    {
        $response = $this->get('/cart');

        $response->assertRedirect('/login');
    }
}
