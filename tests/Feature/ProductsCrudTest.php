<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;

/**
 * Copre:
 * - list, create, show, update (parziale), delete
 * - validazione (price>=0, category esistente)
 * - 401 su endpoint protetti se non autenticati
 */

it('requires auth for product endpoints', function () {
    $this->getJson('/api/products')->assertStatus(401);
    $this->postJson('/api/products', [])->assertStatus(401);
});

it('creates and lists products', function () {
    Sanctum::actingAs(User::factory()->create());

    $category = Category::factory()->create();

    // Create
    $payload = ['name'=>'Margherita','price'=>6.50,'stock'=>100,'category_id'=>$category->id];
    $this->postJson('/api/products', $payload)
        ->assertCreated()
        ->assertJsonPath('data.name','Margherita');

    // Index
    $this->getJson('/api/products')
        ->assertOk()
        ->assertJsonStructure(['data'=>[['id','name','price','stock']]]);
});

it('shows, updates (partial) and deletes a product', function () {
    Sanctum::actingAs(User::factory()->create());

    $p = Product::factory()->create(['name'=>'Diavola','price'=>8.00,'stock'=>50]);

    // Show
    $this->getJson("/api/products/{$p->id}")
        ->assertOk()
        ->assertJsonPath('data.name','Diavola');

    // Partial update (only price)
    $this->putJson("/api/products/{$p->id}", ['price'=>9.50])
        ->assertOk()
        ->assertJsonPath('data.price', 9.50);

    // Delete
    $this->deleteJson("/api/products/{$p->id}")
        ->assertNoContent();

    $this->getJson("/api/products/{$p->id}")->assertStatus(404);
});

it('validates product payload', function () {
    Sanctum::actingAs(User::factory()->create());

    // price negativo -> 422
    $this->postJson('/api/products', ['name'=>'Err','price'=>-1])->assertStatus(422);

    // category non esistente -> 422
    $this->postJson('/api/products', ['name'=>'X','price'=>1.0,'category_id'=>999])->assertStatus(422);
});
