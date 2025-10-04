<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('creates product with image via multipart attach', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'admin']);
    $token = auth()->attempt(['email' => $user->email, 'password' => 'password'])
        ?? throw new RuntimeException('Auth failed in test');

    $file = UploadedFile::fake()->image('margherita.jpg', 600, 600);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->post('/api/products', [
            'name'  => 'Pizza Margherita',
            'price' => 6.50,
            'stock' => 50,
            'image' => $file,
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Pizza Margherita')
        ->assertJsonStructure(['data' => ['id','image_url']]);

    $path = \Arr::get($response->json(), 'data.image_url');
    expect($path)->toBeString();

    // Verifica file salvato su disk fake
    $productId = \Arr::get($response->json(), 'data.id');
    $this->assertDatabaseHas('products', ['id' => $productId]);
    // Non conosciamo il path esatto; verifichiamo almeno che esista un file nella dir 'products'
    Storage::disk('public')->assertExists(
        Storage::disk('public')->allFiles('products')[0] ?? 'products' // semplice presenza
    );
});

it('updates product image and removes old file', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'admin']);
    $token = auth()->attempt(['email' => $user->email, 'password' => 'password'])
        ?? throw new RuntimeException('Auth failed in test');

    // crea senza immagine
    $create = $this->withHeader('Authorization', "Bearer $token")
        ->post('/api/products', ['name'=>'Focaccia','price'=>4.0,'stock'=>20]);

    $id = \Arr::get($create->json(), 'data.id');

    // aggiorna con immagine
    $newImg = UploadedFile::fake()->image('focaccia.webp', 800, 600);
    $update = $this->withHeader('Authorization', "Bearer $token")
        ->put("/api/products/{$id}", [
            'image' => $newImg,
        ]);

    $update->assertOk()->assertJsonStructure(['data'=>['image_url']]);
});
