<?php

use App\Enums\PaymentMethod;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;

/**
 * Copre:
 * - 404: ordine senza receipt
 * - 200: ordine pagato -> PDF stream
 */

it('returns 404 for receipt pdf when order is not paid', function () {
    Sanctum::actingAs(User::factory()->create());

    $order = Order::factory()->create([
        'status' => OrderStatus::OPEN->value,
        'type' => OrderType::IN_STORE->value,
        'total' => 0,
    ]);

    $p = Product::factory()->create(['price'=>6.50]);
    OrderItem::factory()->create([
        'order_id'=>$order->id,
        'product_id'=>$p->id,
        'quantity'=>1,
        'price'=>6.50,
        'subtotal'=>6.50,
    ]);
    $order->update(['total'=>6.50]);

    $this->get("/api/orders/{$order->id}/receipt/pdf")->assertStatus(404);
});

it('streams receipt pdf for paid order', function () {
    Sanctum::actingAs(User::factory()->create());

    $order = Order::factory()->create([
        'status' => OrderStatus::PAID->value,
        'type' => OrderType::IN_STORE->value,
        'total' => 6.50,
    ]);

    $p = Product::factory()->create(['price'=>6.50]);
    OrderItem::factory()->create([
        'order_id'=>$order->id,
        'product_id'=>$p->id,
        'quantity'=>1,
        'price'=>6.50,
        'subtotal'=>6.50,
    ]);

    Receipt::factory()->create([
        'order_id' => $order->id,
        'total' => 6.50,
        'payment_method' => PaymentMethod::CASH->value,
        'issued_at' => Carbon::now(),
    ]);

    $this->get("/api/orders/{$order->id}/receipt/pdf")
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
