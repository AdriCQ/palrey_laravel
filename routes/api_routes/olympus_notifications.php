<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Olympus')->middleware('auth:sanctum')->group(function () {
  Route::get('/', 'NotificationController@getUnread');

  Route::middleware(['ol.auth.admin', 'ol.auth.developer'])->group(function () {
    Route::get('/a-unread', 'NotificationController@aGetUnread');
  });
});
