<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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
Auth::routes();
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [ProductController::class, 'importFile'])->name('import');
    Route::post('/import-field-mapping', [ProductController::class, 'importFileMapping']);
    Route::post('/save-import-data', [ProductController::class, 'saveImportData'])->name('save-import-data');

    Route::get('products', [ProductController::class, 'index'])->name('products');

    Route::get('/product-list/{id?}', [ProductController::class, 'productList'])->name('product-list');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
