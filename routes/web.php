<?php

use Illuminate\Support\Facades\Route;
use Modules\Pos\Http\Controllers\PosController;
use Modules\Pos\Livewire\Interface\Home;

use Modules\Pos\Livewire\Pos\Lists as PosLists;
use Modules\Pos\Livewire\Pos\Create as PosCreate;
use Modules\Pos\Livewire\Pos\Show as PosShow;

use Modules\Pos\Livewire\ProductCategory\Lists as CategoryLists;
use Modules\Pos\Livewire\ProductCategory\Create as CategoryCreate;
use Modules\Pos\Livewire\ProductCategory\Show as CategoryShow;

use Modules\Pos\Livewire\Product\Lists as ProductLists;
use Modules\Pos\Livewire\Product\Create as ProductCreate;
use Modules\Pos\Livewire\Product\Show as ProductShow;
// use Modules\Pos\Livewire\Pos\Ui as PosUi;

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

Route::middleware('identify-kover')->group(function () {
    Route::get('/pos/overview', PosLists::class)->name('pos.overview');
    Route::get('/pos/create', PosCreate::class)->name('pos.create');
    Route::get('/pos/{pos}', PosShow::class)->name('pos.show');

    Route::prefix("pos/ui")->group(function() {
        Route::get('/{pos}', Home::class)->name('pos.ui');
    });

    // Product Categories
    Route::prefix('product-categories')->name('product-categories.')->group(function () {
        Route::get('/lists', CategoryLists::class)->name('lists');
        Route::get('/create', CategoryCreate::class)->name('create');
        Route::get('/{category}', CategoryShow::class)->name('show');
    });

    // Product
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/lists', ProductLists::class)->name('lists');
        Route::get('/create', ProductCreate::class)->name('create');
        Route::get('/{product}', ProductShow::class)->name('show');
    });

});
