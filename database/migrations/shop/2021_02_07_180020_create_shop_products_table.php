<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopProductsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('shop_products', function (Blueprint $table) {
      $table->id();
      $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
      $table->string('title', 256);
      $table->longText('description');
      $table->foreignId('image_id')->constrained('shop_images');
      // Price
      $table->unsignedDecimal('production_price', 8, 2)->nullable();
      $table->unsignedDecimal('regular_price', 8, 2)->nullable();
      $table->unsignedDecimal('sell_price', 8, 2);
      // Stock
      $table->boolean('onsale')->default(false);
      $table->unsignedInteger('stock_qty')->default(0);
      $table->string('stock_status')->default('limited');
      $table->unsignedBigInteger('sold')->default('0');
      // Shipping
      $table->unsignedInteger('weight')->default(0);
      $table->string('dimensions')->nullable();
      $table->unsignedDecimal('tax', 8, 2)->default(0);
      // Wholesale
      $table->boolean('wholesale')->default(false);
      $table->unsignedMediumInteger('wholesale_min')->nullable();
      $table->unsignedDecimal('wholesale_price', 8, 2)->nullable();
      // Attributes
      $table->json('attributes')->nullable();
      // Rating
      $table->unsignedInteger('rating_count')->default(0);
      $table->unsignedTinyInteger('rating_average')->default(0);
      // Category
      $table->foreignId('category_id')->constrained('shop_categories');
      $table->json('tags')->nullable();
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
    Schema::dropIfExists('shop_products');
  }
}
