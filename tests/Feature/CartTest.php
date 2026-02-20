<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    public function test_checkout_creates_order_and_clears_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 10000
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->postJson('/api/cart/checkout');

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 20000
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id
        ]);
    }
}