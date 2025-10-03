<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrderItem> */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first() ?? Product::factory()->create();
        $qty = $this->faker->numberBetween(1, 3);
        $price = (float) $product->price;
        return [
            'order_id' => Order::query()->inRandomOrder()->value('id') ?? Order::factory(),
            'product_id' => $product->id,
            'quantity' => $qty,
            'price' => $price,
            'subtotal' => $qty * $price,
        ];
    }
}
