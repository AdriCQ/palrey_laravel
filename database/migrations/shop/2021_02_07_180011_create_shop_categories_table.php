<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopCategoriesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('shop_categories', function (Blueprint $table) {
      $table->id();
      $table->string('tag')->unique();
      $table->string('title');
      $table->text('description')->nullable();
      $table->unsignedBigInteger('parent_id')->nullable();
    });

    Schema::table('shop_categories', function (Blueprint $table) {
      $table->foreign('parent_id')->references('id')->on('shop_categories')->onUpdate('cascade')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('shop_categories');
  }
}
