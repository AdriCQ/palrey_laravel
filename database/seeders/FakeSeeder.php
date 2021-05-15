<?php

namespace Database\Seeders;

use App\Models\Shop\Image;
use Database\Seeders\Olympus\AnnouncementSeeder;
use Database\Seeders\Shop\CategorySeeder;
use Database\Seeders\Shop\ProductSeeder;
use Database\Seeders\Olympus\ApplicationSeeder;
use Database\Seeders\Shop\OrderSeeder;
use Database\Seeders\User\PermissionSeeder;
use Database\Seeders\User\UserSeeder;
use Illuminate\Database\Seeder;

class FakeSeeder extends Seeder
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
      PermissionSeeder::class,
      UserSeeder::class,
      ApplicationSeeder::class,
      CategorySeeder::class,
      ProductSeeder::class,
      OrderSeeder::class,
    ]);
    AnnouncementSeeder::fakeSeed(10, 1);
  }
}
