<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_cancel_pending_order()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/orders/{$order->id}/cancel");

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);
    }

    public function test_admin_can_update_order_status()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $order = Order::factory()->create([
            'status' => 'pending'
        ]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'paid'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid'
        ]);
    }
}