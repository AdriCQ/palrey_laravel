<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopOrderProductsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('shop_order_products', function (Blueprint $table) {
      $table->id();
      $table->foreignId('order_id')->constrained('shop_orders')->cascadeOnDelete();
      $table->foreignId('product_id')->constrained('shop_products')->cascadeOnDelete();
      $table->unsignedSmallInteger('product_qty')->default(1);
      $table->json('product_details')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('shop_order_products');
  }
}
