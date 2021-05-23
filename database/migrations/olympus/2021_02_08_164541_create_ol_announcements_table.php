<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOlAnnouncementsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('ol_announcements', function (Blueprint $table) {
      $table->id();
      $table->boolean('active')->default(false);
      $table->string('type', 64);
      $table->string('title')->nullable();
      $table->string('link')->nullable();
      $table->longText('html')->nullable();
      $table->text('text')->nullable();
      $table->string('icon', 64)->nullable();
      $table->unsignedBigInteger('image_id')->nullable();
      $table->timestamps();
    });

    Schema::table('ol_announcements', function (Blueprint $table) {
      $table->foreign('image_id')->references('id')->on('shop_images')->onUpdate('cascade')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('ol_announcements');
  }
}
