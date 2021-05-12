<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\User')->group(function () {

  /**
   * -----------------------------------------
   *	Auth Routes
   * -----------------------------------------
   */
  Route::post('/login', 'AuthController@login');
  Route::post('/su-login', 'AuthController@sudoLogin');
  Route::post('/register', 'AuthController@register');
  Route::get('/check', 'AuthController@checkAuth')->middleware('auth:sanctum');

  Route::middleware(['auth:sanctum', 'ol.auth.admin'])->group(function () {
    Route::get('/filter', 'UserController@filter');
    Route::get('/details', 'UserController@details');
  });
});
