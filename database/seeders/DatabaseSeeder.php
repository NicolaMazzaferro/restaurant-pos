<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call specific seeders to keep logic modular
        $this->call([
            UsersSeeder::class,
            CatalogSeeder::class,
            SampleOrdersSeeder::class,
        ]);
    }
}
