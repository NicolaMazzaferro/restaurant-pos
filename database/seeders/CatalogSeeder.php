<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/** Seeds categories & products */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Categorie canoniche (slug NOT NULL + UNIQUE)
        $defaults = [
            ['name' => 'Pizze',     'sort_order' => 10],
            ['name' => 'Antipasti', 'sort_order' => 5],
            ['name' => 'Bevande',   'sort_order' => 20],
            ['name' => 'Birre',     'sort_order' => 25],
            ['name' => 'Dolci',     'sort_order' => 30],
            ['name' => 'Vini',      'sort_order' => 26],
        ];

        foreach ($defaults as $row) {
            $name = $row['name'];
            $slug = Str::slug($name);

            // Se per qualche motivo esistesse già uno slug uguale ma con name diverso, rendilo unico
            if (Category::where('slug', $slug)->where('name', '!=', $name)->exists()) {
                $slug .= '-' . Str::lower(Str::random(4));
            }

            Category::firstOrCreate(
                ['name' => $name], // unique per name
                [
                    'slug'       => $slug,
                    'is_active'  => true,
                    'sort_order' => $row['sort_order'],
                ]
            );
        }

        // Prodotti random (factory deve assegnare un category_id valido o crearne uno)
        Product::factory()->count(25)->create();

        // Alcuni prodotti canonici (utili per demo/test)
        $pizzaId   = Category::where('name','Pizze')->value('id');
        $bevandeId = Category::where('name','Bevande')->value('id');

        Product::updateOrCreate(
            ['name' => 'Margherita'],
            ['category_id' => $pizzaId, 'price' => 6.50, 'stock' => 100]
        );
        Product::updateOrCreate(
            ['name' => 'Diavola'],
            ['category_id' => $pizzaId, 'price' => 8.00, 'stock' => 80]
        );
        Product::updateOrCreate(
            ['name' => 'Acqua Naturale 0.5L'],
            ['category_id' => $bevandeId, 'price' => 1.00, 'stock' => 200]
        );
    }
}
