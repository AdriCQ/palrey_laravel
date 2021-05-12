<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOlAppsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('ol_apps', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->foreignId('owner_id')->constrained('users');
      $table->string('token')->unique();
      $table->unsignedSmallInteger('version');
      $table->unsignedSmallInteger('min_required_version')->default(1);
      $table->boolean('update_required')->default(false);
      $table->json('roadmap');
      $table->unsignedBigInteger('daily_visits')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('ol_apps');
  }
}
