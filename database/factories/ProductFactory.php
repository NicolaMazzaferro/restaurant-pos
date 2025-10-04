<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->unique()->words(2, true)),
            'category_id' => fn () => \App\Models\Category::query()->inRandomOrder()->value('id') ?? \App\Models\Category::factory()->create()->id,
            'price' => $this->faker->randomFloat(2, 2, 20), // €2.00 - €20.00
            'stock' => $this->faker->numberBetween(0, 200),
        ];
    }
}
