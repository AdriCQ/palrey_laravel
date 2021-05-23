<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Shop')->group(function () {

  Route::get('/c-by-id', 'ProductController@cById');
  Route::get('/by-category', 'ProductController@getById');
  Route::get('/filter', 'ProductController@filter');

  Route::middleware('auth:sanctum')->group(function () {
    /**
     * -----------------------------------------
     *	Vendor Actions
     * -----------------------------------------
     */
    Route::middleware('ol.auth.vendor')->group(function () {

      Route::get('/v-by-id', 'ProductController@vById');
      Route::get('/v-list', 'ProductController@listByVendor');
      Route::post('/v-update', 'ProductController@update');
      Route::post('/v-upload-image', 'ProductController@vUploadImage');
      Route::get('/v-remove', 'ProductController@remove');
      Route::get('/v-filter', 'ProductController@filterVendor');
    });
    Route::post('/new', function () {
      App\Models\Shop\Product::query()->insert(
        [
          'owner_id' => 1,
          'title' => 'Title',
          'description' => 'Description',
          'image_id' => 1,
          'sell_price' => 0,
          'category_id' => 2,
          'tags' => json_encode(['product']),
          'created_at' => now()->toDateTimeString(),
          'updated_at' => now()->toDateTimeString()
        ],
      );
    })->middleware('ol.auth.developer');
  });
});
