<?php

use App\Models\Shop\Image;
use App\Models\Shop\Product;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
  return Inertia::render('Home');
})->name('welcome');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
  return Inertia::render('Dashboard');
})->name('dashboard');

Route::prefix('/olympus/app')->group(__DIR__ . '/ui_routes/olympus_apps.php');
