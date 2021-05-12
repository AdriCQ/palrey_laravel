<?php

namespace Database\Seeders\Olympus;

use App\Models\Olympus\App as OlympusApplication;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
  public function run()
  {
    $this->realSeed();
  }

  public static function realSeed()
  {
    $apps = [
      [
        'title' => 'Olympus Administrator',
        'owner_id' => 1,
        'token' => '1.Olympus_Administrator',
        'version' => 1,
        'settings' => null,
        'roadmap' => json_encode([]),
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString()
      ],
      [
        'title' => 'Palrey Shop',
        'owner_id' => 2,
        'token' => '2.PalreyShop',
        'settings' => json_encode([
          'min_price' => 40,
          'extra_price' => 0
        ]),
        'version' => 0,
        'roadmap' => json_encode([]),
        'created_at' => now()->toDateTimeString(),
        'updated_at' => now()->toDateTimeString()
      ]
    ];

    OlympusApplication::query()->insert($apps);
  }
}
