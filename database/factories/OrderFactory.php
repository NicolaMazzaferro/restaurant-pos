<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'status' => OrderStatus::OPEN->value,
            'type' => $this->faker->randomElement([OrderType::IN_STORE->value, OrderType::TAKEAWAY->value]),
            'total' => 0,
        ];
    }
}
