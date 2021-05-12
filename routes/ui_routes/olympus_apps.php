<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Olympus')->group(function () {
  Route::get('/download', 'AppController@download');
});
