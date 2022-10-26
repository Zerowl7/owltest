<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;
use App\Models\OrderItem;
use App\Models\Product;
use Faker\Generator as Faker;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = new Faker();
        
        $orders =  Order::all();
        $discont_proc = [5, 10, 15];
        $products =  Product::all();
        $stocks = [];
        foreach ($products as $product) {
            $stocks[$product->id] = ['stock' => $product->stock, 'price' => $product->price];
        }

        $rows = [];
        foreach ($orders as $order) {
            $kolvo =  $faker->numberBetween($min = 5, $max = 10);
            for ($i = 0; $i < $kolvo; $i++) {
                $key_product = array_rand($stocks);
                $stock = $stocks[$key_product]['stock'];
                if ($stock == 0) {
                    continue;
                }
                $kol = $faker->numberBetween($min = 1, $max =  $stock > 1 ? $stock / 2 : 1);

                $cost = $stocks[$key_product]['price'] * $kol;
                $discount =  $cost / 100 * $discont_proc[array_rand($discont_proc)];
                $row = [
                    'count' => $kol,
                    'discount' => $discount,
                    'price' => $stocks[$key_product]['price'],
                    'cost' => $cost,
                    'order_id' => $order->id,
                    'product_id' => $key_product,
                ];
                OrderItem::create($row);
                $stocks[$key_product]['stock'] -= $kol;
            }
        }

    }
}
