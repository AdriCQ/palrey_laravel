<?php

namespace Database\Seeders\User;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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

  public function fakeSeed(int $limit = 10, int $repeats = 1)
  {
    $this->realSeed();
    $faker = Factory::create();
    for ($r = 0; $r < $repeats; $r++) {
      $users = [];
      for ($l = 0; $l < $limit; $l++) {
        array_push($users, [
          'name' => $faker->firstName(),
          // 'last_name' => $faker->lastName,
          'mobile_phone' => $faker->phoneNumber,
          'password' => Hash::make('password'),
          'address' => $faker->address,
        ]);
      }
      User::query()->insert($users);
    }
  }

  /**
   * 
   */
  public static function realSeed()
  {
    $me = new User([
      'name' => 'Adrian Capote',
      // 'last_name' => 'Capote',
      'mobile_phone' => '53375180',
      'password' => Hash::make('password'),
      'address' => 'Calle Silencio #32, Palmira, Cienfuegos',
    ]);
    $me->save();
    $me->assignRole('Developer');
    $dcq = new User([
      'name' => 'Darian Capote',
      // 'last_name' => 'Capote',
      'mobile_phone' => '53927128',
      'password' => Hash::make('password'),
      'address' => 'Calle Silencio #32, Palmira, Cienfuegos'
    ]);
    $dcq->save();
    $dcq->assignRole('Developer');

    $admins = [
      /*[
        'name' => 'David H. Palmero Reyes',
        // 'last_name' => 'Pal',
        'mobile_phone' => '58075153',
        'password' => Hash::make('sueños'),
        'address' => ''
      ], [
        'name' => 'Rafael López Rodríguez',
        // 'last_name' => 'Pal',
        'mobile_phone' => '53284237',
        'password' => Hash::make('Valeria1997'),
        'address' => ''
      ],*/ [
        'name' => 'Liz Manso Gutiérrez',
        // 'last_name' => 'Pal',
        'mobile_phone' => '55190107',
        'password' => Hash::make('Liz.990707'),
        'address' => ''
      ]
    ];
    foreach ($admins as $_user) {
      $user = new User($_user);
      $user->save();
      $user->assignRole('Admin');
    }
  }
}
