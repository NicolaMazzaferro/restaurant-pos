<?php

use App\Enums\OrderType;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test unitario sul Service:
 * - calcolo del totale corretto
 * - creazione di items coerente
 * Nota: usa il DB (transazioni) -> includiamo RefreshDatabase anche qui.
 */
uses(RefreshDatabase::class);

it('OrderService creates order with correct total and items', function () {
    $user = User::factory()->create();

    $p1 = Product::factory()->create(['price'=>5.00]);
    $p2 = Product::factory()->create(['price'=>7.25]);

    /** @var OrderService $svc */
    $svc = app(OrderService::class);

    $order = $svc->create(
        userId: $user->id,
        items: [
            ['product_id'=>$p1->id, 'quantity'=>2],
            ['product_id'=>$p2->id, 'quantity'=>3],
        ],
        type: OrderType::IN_STORE->value,
        paymentMethod: null
    );

    expect($order->items)->toHaveCount(2);
    expect((float)$order->total)->toEqual(2*5.00 + 3*7.25);
});
