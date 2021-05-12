<?php

namespace Database\Seeders;

use App\Models\Shop\Image;
use Database\Seeders\Shop\CategorySeeder;
use Database\Seeders\Shop\ProductSeeder;
use Database\Seeders\Olympus\ApplicationSeeder;
use Database\Seeders\User\PermissionSeeder;
use Database\Seeders\User\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    Image::query()->insert([
      'title' => 'Olympus Logo',
      'paths' => json_encode([
        'xs' => '',
        'sm' => '',
        'md' => '',
        'lg' => '',
        'xl' => '',
      ]),
      'tags' => json_encode(['olympus'])
    ]);
    $this->call([
      PermissionSeeder::class
    ]);
    $this->realSeed();
  }

  /**
   * 
   */
  private function realSeed()
  {
    CategorySeeder::realSeed();
    UserSeeder::realSeed();
    ApplicationSeeder::realSeed();
    ProductSeeder::realSeed();
  }
}
