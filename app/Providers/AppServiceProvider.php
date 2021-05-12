<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function boot()
  {
    // Register migrations
    $this->setDbPaths();
  }

  /**
   * 
   */
  private function setDbPaths()
  {
    $MODULES = ['laravel', 'olympus', 'shop', 'shop/pivot', 'users'];
    $MIGRATIONS_FOLDER = [];
    for ($i = 0; $i < count($MODULES); $i++) {
      $MIGRATIONS_FOLDER[$i] = 'database/migrations/' . $MODULES[$i];
    }
    // Core
    $this->loadMigrationsFrom($MIGRATIONS_FOLDER);
  }
}
