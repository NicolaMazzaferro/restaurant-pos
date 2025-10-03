<?php

use App\Models\User;

/**
 * Verifica login con credenziali corrette/errate.
 * Nota: non serve Sanctum::actingAs qui perché testiamo l'endpoint login.
 */

it('logs in with valid credentials and returns token', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $res = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $res->assertOk()
        ->assertJsonStructure(['token','token_type']);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'foo@bar.com',
        'password' => bcrypt('password'),
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'foo@bar.com',
        'password' => 'wrong',
    ])->assertStatus(401);
});
