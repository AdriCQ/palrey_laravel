<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopOrdersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('shop_orders', function (Blueprint $table) {
      $table->id();
      $table->foreignId('app_id')->constrained('ol_apps');
      $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
      $table->unsignedDecimal('tax', 8, 2)->default(0);
      $table->unsignedDecimal('total_price', 8, 2)->default(0);
      $table->timestamp('delivery_time')->nullable();
      $table->text('shipping_address');
      $table->text('message')->nullable();
      $table->json('coordinates')->nullable();
      $table->unsignedSmallInteger('total_products')->default(1);
      $table->string('status')->default('processing');
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
    Schema::dropIfExists('shop_orders');
  }
}
