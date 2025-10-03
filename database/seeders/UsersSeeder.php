<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** Seeds admin and a couple of cashiers */
class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Cashiers
        User::factory()->count(2)->create([
            'password' => bcrypt('password'),
        ]);
    }
}
