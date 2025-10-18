<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

/**
 * Feature Test - Printer API (protetta da auth)
 *
 * Questi test verificano che le rotte /api/printers funzionino correttamente
 * per utenti autenticati. L'autenticazione viene simulata tramite Sanctum.
 */

uses(RefreshDatabase::class);

beforeEach(function () {
    // Crea un utente fittizio
    $this->user = User::factory()->create();

    // Simula login (autenticazione Sanctum)
    Sanctum::actingAs($this->user);
});

it('can fetch printer list (empty at start)', function () {
    $response = $this->getJson('/api/printers');

    $response
        ->assertOk()
        ->assertJson(['data' => []]);
});

it('can save a list of printers and retrieve them', function () {
    $payload = [
        'printers' => [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Stampante Cucina',
                'model' => 'bixolon',
                'header' => 'Pizzeria Test',
                'address' => 'Via Roma 10',
                'city' => 'Milano',
                'phone' => '123456789',
                'vat' => 'IT1234567890',
                'printer_port' => '\\\\.\\COM3',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Stampante Sala',
                'model' => 'epson',
                'header' => 'Pizzeria Test',
                'address' => 'Via Milano 20',
                'city' => 'Milano',
                'phone' => '987654321',
                'vat' => 'IT1234567890',
                'printer_port' => '192.168.1.21:9101',
            ],
        ],
    ];

    $response = $this->postJson('/api/printers', $payload);

    $response
        ->assertOk()
        ->assertJson(['message' => 'Printers saved successfully.']);

    $this->assertDatabaseCount('printers', 2);

    $fetch = $this->getJson('/api/printers');
    $fetch->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'Stampante Cucina');
});

it('rejects malformed data', function () {
    $payload = [
        'printers' => [
            ['name' => 'SoloNome'], // manca id
        ],
    ];

    $response = $this->postJson('/api/printers', $payload);
    $response->assertStatus(422); // errore di validazione
});
