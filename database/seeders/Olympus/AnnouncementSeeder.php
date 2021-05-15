<?php

namespace Database\Seeders\Olympus;

use App\Models\Olympus\Announcement;
use App\Models\Olympus\Application;
use App\Models\Olympus\Image;
use Illuminate\Database\Seeder;
use Faker\Factory;

class AnnouncementSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
  }

  public static function realSeed()
  {
    $faker = Factory::create();
    $ann = [];
    for ($l = 0; $l < 12; $l++) {
      array_push($ann, [
        'active' => false,
        'title' => $faker->words(3, true),
        'type' => $faker->randomElement(Announcement::$TYPES),
        'text' => $faker->text,
        'icon' => $faker->randomElement(['mdi-cart', null]),
        // 'image_id' => 1,
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString(),
      ]);
    }
    Announcement::query()->insert($ann);
  }

  public static function fakeSeed(int $limit = 10, int $repeat = 1)
  {
    $faker = Factory::create();
    for ($r = 0; $r < $repeat; $r++) {
      $ann = [];
      for ($l = 0; $l < $limit; $l++) {
        array_push($ann, [
          'active' => $faker->boolean,
          'title' => $faker->words(3, true),
          'type' => $faker->randomElement(Announcement::$TYPES),
          'link' => $faker->word(),
          'text' => $faker->text,
          'icon' => $faker->randomElement(['mdi-cart', null]),
          'image_id' => 1,
          'created_at' => now()->toDateTimeString(),
          'updated_at' => now()->toDateTimeString(),
        ]);
      }
      Announcement::query()->insert($ann);
    }
  }
}
