<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Laravel\Sanctum\Sanctum;
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * Copre:
 * - creazione ordine UNPAID (niente receipt)
 * - creazione ordine PAID (receipt presente)
 * - calcolo del totale
 * - validazione items (qty>=1, product esistente)
 * - 401 se non autenticato
 */

it('requires auth for orders endpoints', function () {
    $this->getJson('/api/orders')->assertStatus(401);
    $this->postJson('/api/orders', [])->assertStatus(401);
});

it('creates an unpaid order and returns correct total without receipt', function () {
    Sanctum::actingAs(User::factory()->create());

    $p1 = Product::factory()->create(['price'=>6.50, 'stock'=>100]);
    $p2 = Product::factory()->create(['price'=>8.00, 'stock'=>100]);

    $payload = [
        'type' => 'in_store',
        'items' => [
            ['product_id'=>$p1->id, 'quantity'=>2],
            ['product_id'=>$p2->id, 'quantity'=>1],
        ],
    ];

    $res = $this->postJson('/api/orders', $payload)->assertCreated();
    $total = 2*6.50 + 1*8.00;

    $res->assertJson(fn (AssertableJson $json) =>
        $json->where('data.total', fn ($v) => abs((float)$v - $total) < 0.0001)
            ->missing('data.receipt') // nessuna ricevuta per ordine non pagato
            ->etc()
    );

    // verifica db
    $orderId = $res->json('data.id');
    $storedTotal = (float) Order::find($orderId)->total;
    expect($storedTotal)->toEqualWithDelta($total, 0.0001);
});

it('creates a paid order, generates receipt and decrements stock', function () {
    Sanctum::actingAs(User::factory()->create());

    $p = Product::factory()->create(['price'=>7.00, 'stock'=>10]);

    $res = $this->postJson('/api/orders', [
        'type' => 'takeaway',
        'payment_method' => 'cash',
        'items' => [
            ['product_id'=>$p->id, 'quantity'=>3],
        ],
    ])->assertCreated();

    $res->assertCreated()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('data.status', 'paid')
                ->where('data.receipt.payment_method', 'cash')
                ->where('data.total', fn ($v) => abs((float)$v - 21.00) < 0.0001)
                ->etc()
        );

    // stock decrementato (se previsto dal service)
    $p->refresh();
    expect($p->stock)->toBe(7);
});

it('validates order payload and items', function () {
    Sanctum::actingAs(User::factory()->create());

    // qty 0 => 422
    $p = Product::factory()->create(['price'=>5.00]);
    $this->postJson('/api/orders', [
        'type'=>'in_store',
        'items'=>[['product_id'=>$p->id,'quantity'=>0]],
    ])->assertStatus(422);

    // product inexistant => 422
    $this->postJson('/api/orders', [
        'type'=>'in_store',
        'items'=>[['product_id'=>999,'quantity'=>1]],
    ])->assertStatus(422);
});
