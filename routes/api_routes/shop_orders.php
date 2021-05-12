<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Shop')->group(function () {
  Route::post('/check', 'OrderController@checkOrder');

  Route::middleware('auth:sanctum')->group(function () {
    Route::post('/', 'OrderController@cStore');
    // Route::post('/wholesale', 'OrderController@cWholesaleStore');
    Route::get('/c-list', 'OrderController@cList');
    Route::get('/c-cancel', 'OrderController@cCancel');
    Route::get('/c-count', 'OrderController@cCount');
    /**
     * -----------------------------------------
     *	Vendor Actions
     * -----------------------------------------
     */
    Route::middleware('ol.auth.vendor')->group(function () {
      Route::get('/v-list', 'OrderController@vList');
      Route::get('/v-count', 'OrderController@vCount');
      Route::post('/v-change-status', 'OrderController@vChangeStatus');
    });

    Route::middleware('ol.auth.developer')->get('/v-delete', 'OrderController@vDelete');
  });
});
