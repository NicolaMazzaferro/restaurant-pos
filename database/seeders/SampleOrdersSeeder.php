<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/** Seeds a few sample orders (one OPEN, one PAID with receipt) */
class SampleOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // any user

        // OPEN order
        DB::transaction(function () use ($user) {
            $order = Order::create([
                'user_id' => $user->id,
                'status'  => OrderStatus::OPEN->value,
                'type'    => OrderType::IN_STORE->value,
                'total'   => 0,
            ]);

            $p1 = Product::where('name','Margherita')->first() ?? Product::inRandomOrder()->first();
            $p2 = Product::where('name','Diavola')->first() ?? Product::inRandomOrder()->first();

            $rows = [
                ['product'=>$p1, 'qty'=>2],
                ['product'=>$p2, 'qty'=>1],
            ];

            $total = 0;
            foreach ($rows as $r) {
                $subtotal = $r['qty'] * (float)$r['product']->price;
                OrderItem::create([
                    'order_id'=>$order->id,
                    'product_id'=>$r['product']->id,
                    'quantity'=>$r['qty'],
                    'price'=>$r['product']->price,
                    'subtotal'=>$subtotal,
                ]);
                $total += $subtotal;
            }

            $order->update(['total' => $total]);
        });

        // PAID order + receipt
        DB::transaction(function () use ($user) {
            $order = Order::create([
                'user_id' => $user->id,
                'status'  => OrderStatus::PAID->value,
                'type'    => OrderType::TAKEAWAY->value,
                'total'   => 0,
            ]);

            $p = Product::where('name','Margherita')->first() ?? Product::inRandomOrder()->first();
            $qty = 3;
            $subtotal = $qty * (float)$p->price;

            OrderItem::create([
                'order_id'=>$order->id,
                'product_id'=>$p->id,
                'quantity'=>$qty,
                'price'=>$p->price,
                'subtotal'=>$subtotal,
            ]);

            $order->update(['total' => $subtotal]);

            Receipt::create([
                'order_id' => $order->id,
                'total' => $subtotal,
                'payment_method' => PaymentMethod::CASH->value,
                'issued_at' => Carbon::now(),
            ]);
        });
    }
}
