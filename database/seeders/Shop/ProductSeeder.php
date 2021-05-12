<?php

namespace Database\Seeders\Shop;

use App\Models\Shop\Category;
use App\Models\Shop\Image;
use App\Models\Shop\Product;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->fakeSeed();
  }

  /**
   * 
   */
  public static function realSeed()
  {
    Image::query()->insert([
      [
        'title' => 'Polvorones-cover',
        'paths' => json_encode([
          "sm" => "/storage/shop/images/product/sm_2.jpg",
          "md" => "/storage/shop/images/product/md_2.jpg",
          "lg" => "/storage/shop/images/product/lg_2.jpg",
        ]),
        'tags' => json_encode(['desserts'])
      ],
      [
        'title' => 'Empanadas-cover',
        'paths' => json_encode([
          "sm" => "/storage/shop/images/product/sm_3.jpg",
          "md" => "/storage/shop/images/product/md_3.jpg",
          "lg" => "/storage/shop/images/product/lg_3.jpg",
        ]),
        'tags' => json_encode(['desserts'])
      ],
      [
        'title' => 'Nubes-cover',
        'paths' => json_encode([
          "sm" => "/storage/shop/images/product/sm_4.jpg",
          "md" => "/storage/shop/images/product/md_4.jpg",
          "lg" => "/storage/shop/images/product/lg_4.jpg",
        ]),
        'tags' => json_encode(['desserts'])
      ],
      [
        'title' => 'Enchocolatado-cover',
        'paths' => json_encode([
          "sm" => "/storage/shop/images/product/sm_5.jpg",
          "md" => "/storage/shop/images/product/md_5.jpg",
          "lg" => "/storage/shop/images/product/lg_5.jpg",
        ]),
        'tags' => json_encode(['desserts'])
      ],
      [
        'title' => 'Espejuelos-cover',
        'paths' => json_encode([
          "sm" => "/storage/shop/images/product/sm_6.jpg",
          "md" => "/storage/shop/images/product/md_6.jpg",
          "lg" => "/storage/shop/images/product/lg_6.jpg",
        ]),
        'tags' => json_encode(['desserts'])
      ],

      [
        'title' => 'Pasteles-cover',
        'paths' => json_encode([
          "sm" => "/storage/shop/images/product/sm_6.jpg",
          "md" => "/storage/shop/images/product/md_6.jpg",
          "lg" => "/storage/shop/images/product/lg_6.jpg",
        ]),
        'tags' => json_encode(['desserts'])
      ]
    ]);
    $products = [
      [
        'owner_id' => 3,
        'title' => 'Polvorón',
        'description' => 'Polvorones hechos a base de harina de trigo, manteca pastelera y toques de sabor de vainilla, chocolate y miel de abeja. Presentación con un top de crema de guayaba',
        'image_id' => 2,
        'production_price' => 3,
        'regular_price' => 3,
        'sell_price' => 4,
        'sold' => 0,
        'onsale' => true,
        'stock_qty' => 10,
        'stock_status' => 'sold_out',
        'wholesale' => true,
        'wholesale_min' => 1,
        'wholesale_price' => 3,
        'weight' => 10,
        'dimensions' => '5|5|5',
        'tax' => 0,
        'attributes' => null,
        'category_id' => 2,
        'tags' => json_encode(['dulces']),
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString()
      ],
      [
        'owner_id' => 3,
        'title' => 'Empanada',
        'description' => 'Empanadas hechas con aceite vegetal, harina de trigo y con rellenos en crema',
        'image_id' => 3,
        'production_price' => 3,
        'regular_price' => 3,
        'sell_price' => 3,
        'sold' => 0,
        'onsale' => true,
        'stock_qty' => 10,
        'stock_status' => 'sold_out',
        'wholesale' => true,
        'wholesale_min' => 1,
        'wholesale_price' => 3,
        'weight' => 10,
        'dimensions' => '5|5|5',
        'tax' => 0,
        'attributes' => null,
        'category_id' => 2,
        'tags' => json_encode(['dulces']),
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString()
      ],

      [
        'owner_id' => 3,
        'title' => 'Nube',
        'description' => 'Nubes con textura única similar a los malvaviscos hechos a base de harina de trigo y merengue, con rellenos en crema',
        'image_id' => 4,
        'production_price' => 3,
        'regular_price' => 3,
        'sell_price' => 4,
        'sold' => 0,
        'onsale' => true,
        'stock_qty' => 10,
        'stock_status' => 'sold_out',
        'wholesale' => true,
        'wholesale_min' => 1,
        'wholesale_price' => 3,
        'weight' => 10,
        'dimensions' => '5|5|5',
        'tax' => 0,
        'attributes' => null,
        'category_id' => 2,
        'tags' => json_encode(['dulces']),
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString()
      ],
      [
        'owner_id' => 3,
        'title' => 'Chocolatina',
        'description' => 'Polvorones hechos a base de harina de trigo, manteca pastelera y toques de sabor de vainilla. Cubiertos con una exquisita capa de chocolate Nutella de alta calidad',
        'image_id' => 5,
        'production_price' => 3,
        'regular_price' => 5,
        'sell_price' => 5,
        'sold' => 0,
        'onsale' => true,
        'stock_qty' => 0,
        'stock_status' => 'sold_out',
        'wholesale' => false,
        'wholesale_min' => 1,
        'wholesale_price' => 4,
        'weight' => 10,
        'dimensions' => '5|5|5',
        'tax' => 0,
        'attributes' => null,
        'category_id' => 2,
        'tags' => json_encode(['dulces']),
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString()
      ],
      [
        'owner_id' => 3,
        'title' => 'Espejuelito',
        'description' => '',
        'image_id' => 6,
        'production_price' => 3,
        'regular_price' => 5,
        'sell_price' => 5,
        'sold' => 0,
        'onsale' => true,
        'stock_qty' => 0,
        'stock_status' => 'sold_out',
        'wholesale' => false,
        'wholesale_min' => 1,
        'wholesale_price' => 4,
        'weight' => 10,
        'dimensions' => '5|5|5',
        'tax' => 0,
        'attributes' => null,
        'category_id' => 2,
        'tags' => json_encode(['dulces']),
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString()
      ],

      [
        'owner_id' => 3,
        'title' => 'Pastel',
        'description' => '',
        'image_id' => 7,
        'production_price' => 3,
        'regular_price' => 5,
        'sell_price' => 5,
        'sold' => 0,
        'onsale' => true,
        'stock_qty' => 0,
        'stock_status' => 'sold_out',
        'wholesale' => false,
        'wholesale_min' => 1,
        'wholesale_price' => 4,
        'weight' => 10,
        'dimensions' => '5|5|5',
        'tax' => 0,
        'attributes' => null,
        'category_id' => 2,
        'tags' => json_encode(['dulces']),
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString()
      ],
    ];
    Product::query()->insert($products);
  }

  private function fakeSeed($limit = 10, $rp = 1)
  {
    $this->realSeed();
    $faker = Factory::create();
    for ($r = 0; $r < $rp; $r++) {
      $products = [];
      for ($l = 0; $l < $limit; $l++) {
        array_push($products, [
          'owner_id' => $faker->numberBetween(1, User::query()->count()),
          'title' => $faker->words(5, true),
          'description' => $faker->text,
          'image_id' => $faker->numberBetween(1, Image::query()->count()),
          'production_price' => $faker->randomFloat(2, 1, 1000),
          'regular_price' => $faker->randomFloat(2, 1, 1000),
          'sell_price' => $faker->randomFloat(2, 1, 1000),
          'sold' => $faker->numberBetween(0, 100),
          'onsale' => $faker->boolean(80),
          'stock_qty' => $faker->numberBetween(1, 100),
          'stock_status' => $faker->randomElement(Product::$STOCK_STATUS),
          'weight' => $faker->numberBetween(1, 1000),
          'dimensions' => $faker->numberBetween(1, 100) . '|' . $faker->numberBetween(1, 100) . '|' . $faker->numberBetween(1, 100),
          // 'tax' => $faker->randomFloat(2, 1, 1000),
          'attributes' => json_encode([
            'colors' => [$faker->colorName, $faker->colorName]
          ]),
          'wholesale' => $faker->boolean,
          'wholesale_min' => $faker->numberBetween(20, 100),
          'wholesale_price' => $faker->randomFloat(2, 1, 1000),
          'rating_count' => $faker->numberBetween(1, 100),
          'rating_average' => $faker->numberBetween(0, 10),
          'category_id' => $faker->numberBetween(1, Category::query()->count()),
          'tags' => json_encode($faker->words()),
          'created_at' => now()->toDateTimeString(),
          'updated_at' => now()->toDateTimeString()
        ]);
      }
      Product::query()->insert($products);
    }
  }
}
