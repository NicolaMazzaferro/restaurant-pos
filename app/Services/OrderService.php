<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class OrderService {
    /**
     * Create order with items and (optionally) mark as paid + create receipt.
     * @param int $userId
     * @param array $items [ ['product_id'=>int,'quantity'=>int,'price'=>?optional], ... ]
     * @param string $type enum OrderType value
     * @param null|string $paymentMethod PaymentMethod value -> if provided, order becomes PAID + receipt
     */
    public function create(int $userId, array $items, string $type, ?string $paymentMethod = null): Order
    {
        return DB::transaction(function() use ($userId, $items, $type, $paymentMethod) {

            $order = Order::create([
                'user_id' => $userId,
                'status' => OrderStatus::OPEN->value,
                'type' => $type,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($items as $row) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->findOrFail($row['product_id']);
                $qty = max(1, (int)$row['quantity']);

                // Prezzo "snapshot": se non passato, usa product->price
                $unit = isset($row['price']) ? (float)$row['price'] : (float)$product->price;
                $subtotal = $unit * $qty;
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $unit,
                    'subtotal' => $subtotal,
                ]);

                // opzionale: decrementa stock se necessario
                if ($product->stock !== null) {
                    $product->decrement('stock', $qty);
                }
            }

            $order->update(['total' => $total]);

            if ($paymentMethod) {
                $order->update(['status' => OrderStatus::PAID->value]);
                Receipt::create([
                    'order_id' => $order->id,
                    'total' => $total,
                    'payment_method' => $paymentMethod,
                    'issued_at' => Carbon::now(),
                ]);
            }

            return $order->load(['items.product','receipt']);
        });
    }
}
