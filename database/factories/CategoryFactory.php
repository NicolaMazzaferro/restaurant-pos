<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Pizze','Bevande','Antipasti','Dolci','Birre','Vini'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'is_active'  => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
