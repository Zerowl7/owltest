<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nette\Utils\Random;

class OrderFactory extends Factory
{

    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $users =  User::all();
        $user_ids = [];
        foreach ($users as $user) {
            if(!$user->is_admin){
              $user_ids[] =  $user->id;  
            }
        }

        return [
            'customer' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber,
            'completed_at' => $this->faker->dateTimeBetween("2022-09-01", now()),
            'type' => $this->faker->randomElement(['online', 'offline']),
            'status' => $this->faker->randomElement(['Active', 'Completed', 'Canceled']),
            'user_id' => $user_ids[array_rand($user_ids)],
        ];
    }
}
