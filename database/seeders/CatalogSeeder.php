<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/** Seeds categories & products */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure some well-known categories exist
        $cats = collect(['Pizze','Antipasti','Bevande','Birre','Dolci','Vini'])
            ->map(fn($name) => Category::firstOrCreate(['name' => $name]));

        // Create products using factory
        Product::factory()->count(25)->create();
        
        // Some canonical items (useful in demos/tests)
        $pizza = Category::where('name','Pizze')->first();
        Product::updateOrCreate(
            ['name' => 'Margherita'],
            ['category_id' => $pizza?->id, 'price' => 6.50, 'stock' => 100]
        );
        Product::updateOrCreate(
            ['name' => 'Diavola'],
            ['category_id' => $pizza?->id, 'price' => 8.00, 'stock' => 80]
        );
        Product::updateOrCreate(
            ['name' => 'Acqua Naturale 0.5L'],
            ['category_id' => Category::where('name','Bevande')->value('id'), 'price' => 1.00, 'stock' => 200]
        );
    }
}
