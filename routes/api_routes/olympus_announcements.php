<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Olympus')->group(function () {
  Route::get('/', 'AnnouncementController@list');
  Route::middleware(['auth:sanctum', 'ol.auth.admin'])->group(function () {
    Route::post('/', 'AnnouncementController@create');
    Route::post('/update', 'AnnouncementController@update');
    Route::get('/remove', 'AnnouncementController@remove');
  });
});
