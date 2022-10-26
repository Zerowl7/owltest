<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $orders =  Order::all();
        $discont_proc = [5, 10, 15];
        $products =  Product::all();
        $stocks = [];
        foreach ($products as $product) {
            $stocks[$product->id] = ['stock' => $product->stock, 'price' => $product->price];
        }

        $rows = [];
        foreach ($orders as $order) {
            $kolvo =  $this->faker->numberBetween($min = 5, $max = 10);
            for ($i = 0; $i < $kolvo; $i++) {
                $key_product = array_rand($stocks);
                $stock = $stocks[$key_product]['stock'];
                if ($stock == 0) {
                    continue;
                }
                $kol = $this->faker->numberBetween($min = 1, $max =  $stock > 1 ? $stock / 2 : 1);

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
                $rows[] = $row;
                $stocks[$key_product]['stock'] -= $kol;
            }
        }

// print_r($rows);
// dd();
       // return $rows;
    }
}
