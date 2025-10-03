<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Receipt> */
class ReceiptFactory extends Factory
{
    protected $model = Receipt::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'total' => $this->faker->randomFloat(2, 5, 60),
            'payment_method' => $this->faker->randomElement([
                PaymentMethod::CASH->value,
                PaymentMethod::CARD->value,
                PaymentMethod::OTHER->value,
            ]),
            'issued_at' => now(),
        ];
    }
}
