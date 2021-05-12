<?php

namespace Database\Seeders\Shop;

// use App\Models\Olympus\App as OlympusApp;
use App\Models\Shop\Order;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->fakeSeed(12, 5);
  }

  private function fakeSeed($limit = 12, $rp = 1)
  {
    $faker = Factory::create();
    for ($r = 0; $r < $rp; $r++) {
      $orders = [];
      for ($l = 0; $l < $limit; $l++) {
        array_push($orders, [
          'app_id' => 2,
          'customer_id' => $faker->numberBetween(1, User::query()->count()),
          'tax' => $faker->randomFloat(2, 0, 1000),
          'total_price' => $faker->randomFloat(2, 0, 10000),
          'shipping_address' => $faker->address,
          'coordinates' => json_encode([
            'lat' => '22.2398885' . $faker->numberBetween(1000000, 8000000),
            'lng' => '-80.390417' . $faker->numberBetween(1000000, 8000000),
          ]),
          'delivery_time' => now()->addHour(1)->toDateTimeString(),
          'total_products' => $faker->numberBetween(1, 100),
          'status' => 'processing',
          // 'status' => $faker->randomElement(Order::$STATUS),
          'created_at' => now()->toDateTimeString(),
          'updated_at' => now()->toDateTimeString()
        ]);
      }
      Order::query()->insert($orders);
    }
  }
}
