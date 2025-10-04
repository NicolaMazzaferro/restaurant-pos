<?php

use App\Models\Order;
use App\Models\Receipt;
use App\Models\User;

use function Pest\Laravel\{actingAs, getJson};

it('returns 404 when receipt does not exist for order', function () {
    $user = User::factory()->create();

    $order = Order::factory()->create([
        'user_id' => $user->id,
    ]);

    actingAs($user, 'sanctum');

    getJson("/api/orders/{$order->id}/receipt")
        ->assertStatus(404)
        ->assertJsonFragment(['message' => 'Receipt not found for this order']);
});

it('returns receipt json when receipt exists', function () {
    $user = User::factory()->create();

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'total'   => 21.50,
    ]);

    // Crea la receipt collegata
    $receipt = Receipt::factory()->for($order)->create([
        'total'          => $order->total,
        'payment_method' => 'cash',
        'issued_at'      => now(),
    ]);

    actingAs($user, 'sanctum');

    getJson("/api/orders/{$order->id}/receipt")
        ->assertOk()
        ->assertJsonPath('data.order.id', $order->id)
        ->assertJsonPath('data.total', (float) $order->total)
        ->assertJsonPath('data.payment_method', 'cash');
});
