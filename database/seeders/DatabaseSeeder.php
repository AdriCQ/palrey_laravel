<?php

namespace Database\Seeders;

use App\Models\Shop\Image;
use Database\Seeders\Olympus\AnnouncementSeeder;
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
      'title' => 'Default Image',
      'paths' => json_encode([
        'sm' => '/img/default.jpg',
        'md' => '/img/default.jpg',
        'lg' => '/img/default.jpg',
      ]),
      'tags' => json_encode(['default'])
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
    AnnouncementSeeder::realSeed();
  }
}
