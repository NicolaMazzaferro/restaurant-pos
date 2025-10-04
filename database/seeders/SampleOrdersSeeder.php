<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleOrdersSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1) cashier di appoggio
            $cashier = User::query()->where('role', 'cashier')->first()
                ?? User::factory()->create(['role' => 'cashier']);

            // 2) categorie assicurate
            $catPizze   = $this->ensureCategory('Pizze',   10);
            $catBevande = $this->ensureCategory('Bevande', 20);
            $catBirre   = $this->ensureCategory('Birre',   25);

            // 3) prodotti assicurati
            $margherita = $this->ensureProduct('Margherita', $catPizze->id, 6.50, 100);
            $diavola    = $this->ensureProduct('Diavola',    $catPizze->id, 7.50, 100);
            $birra33    = $this->ensureProduct('Birra 33cl', $catBirre->id, 3.50, 200);
            $acqua50    = $this->ensureProduct('Acqua 50cl', $catBevande->id, 1.50, 200);

            // 4) scegliamo casi Enum compatibili con il tuo progetto
            $statusCase = $this->pickStatusCase();
            $typeCase   = $this->pickTypeCase();

            // 5) ordine di esempio
            $order = Order::create([
                'user_id' => $cashier->id,
                // Se Order::casts usa le Enum (come da nostro setup), passiamo direttamente i "case"
                'status'  => $statusCase,
                'type'    => $typeCase,
                'total'   => 0,
            ]);

            // 6) righe d'ordine
            $rows = [
                ['product' => $margherita, 'qty' => 2],
                ['product' => $diavola,    'qty' => 1],
                ['product' => $birra33,    'qty' => 2],
                ['product' => $acqua50,    'qty' => 1],
            ];

            $total = 0.0;
            foreach ($rows as $r) {
                $p = $r['product'];
                $qty = (int) $r['qty'];
                $subtotal = $qty * (float) $p->price;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $p->id,
                    'quantity'   => $qty,
                    'price'      => $p->price,
                    'subtotal'   => $subtotal,
                ]);

                $p->decrement('stock', $qty);
                $total += $subtotal;
            }

            $order->update(['total' => $total]);
        });
    }

    private function ensureCategory(string $name, int $sortOrder): Category
    {
        $slug = Str::slug($name);
        return Category::firstOrCreate(
            ['name' => $name],
            ['slug' => $slug, 'is_active' => true, 'sort_order' => $sortOrder]
        );
    }

    private function ensureProduct(string $name, int $categoryId, float $price, int $stock): Product
    {
        return Product::firstOrCreate(
            ['name' => $name],
            ['category_id' => $categoryId, 'price' => $price, 'stock' => $stock]
        );
    }

    /**
     * Trova un case di OrderStatus sensato per "ordine non pagato" tra quelli esistenti nel tuo Enum.
     * Prova per name (UNPAID/PENDING/OPEN/DRAFT/CREATED) e per value (unpaid/pending/open/draft/created).
     * Se non trova nulla, usa il primo case disponibile per evitare errori al seed.
     */
    private function pickStatusCase(): OrderStatus
    {
        $candidates = ['UNPAID','PENDING','OPEN','DRAFT','CREATED','NOT_PAID','unpaid','pending','open','draft','created','not_paid'];
        $cases = OrderStatus::cases();
        foreach ($cases as $case) {
            foreach ($candidates as $want) {
                if (strcasecmp($case->name, $want) === 0) {
                    return $case;
                }
                if (is_string($case->value) && strcasecmp((string)$case->value, $want) === 0) {
                    return $case;
                }
                if (is_int($case->value) && (string)$case->value === $want) {
                    return $case;
                }
            }
        }
        return $cases[0]; // fallback
    }

    /**
     * Trova un case di OrderType sensato per "consumo sul posto" (eat-in/dine-in).
     * Prova per name e per value; altrimenti fallback al primo case.
     */
    private function pickTypeCase(): OrderType
    {
        $candidates = ['EAT_IN','DINE_IN','TAKE_AWAY','TAKEAWAY','DELIVERY','eat_in','dine_in','take_away','takeaway','delivery'];
        $cases = OrderType::cases();
        foreach ($cases as $case) {
            foreach ($candidates as $want) {
                if (strcasecmp($case->name, $want) === 0) {
                    return $case;
                }
                if (is_string($case->value) && strcasecmp((string)$case->value, $want) === 0) {
                    return $case;
                }
                if (is_int($case->value) && (string)$case->value === $want) {
                    return $case;
                }
            }
        }
        return $cases[0]; // fallback
    }
}
