<?php

namespace Database\Seeders\Shop;

use App\Models\Shop\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->realSeed();
  }

  /**
   * 
   */
  public static function realSeed()
  {
    Category::query()->insert(
      [
        'title' => 'Culinario',
        'tag' => 'culinary',
        'description' => 'Pizzas, Pastas, Bebidas, Dulces',
      ]
    );
    Category::query()->insert(
      [
        'title' => 'Dulces',
        'tag' => 'culinary.desserts',
        'description' => 'Dulces',
        'parent_id' => 1
      ]
    );
  }
}
