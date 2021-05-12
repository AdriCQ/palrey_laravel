<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Olympus')->group(function () {
  Route::get('/updates', 'AppController@checkForUpdates');
  Route::get('/token', 'AppController@getToken');

  Route::middleware(['auth:sanctum', 'ol.auth.admin'])->group(function () {
    Route::get('/', 'AppController@getInfo');
    Route::post('/settings', 'AppController@setSettings');
  });
});
