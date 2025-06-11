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

use Modules\Pos\Livewire\Order\Lists as OrderLists;
use Modules\Pos\Livewire\Order\Show as OrderShow;

use Modules\Pos\Livewire\Session\Lists as PosSessionLists;
use Modules\Pos\Livewire\Pos\Show as PosSessionShow;
// use Modules\Pos\Livewire\Pos\Ui as PosUi;

use Modules\Pos\Livewire\Floor\Lists as FloorLists;
use Modules\Pos\Livewire\Floor\Create as FloorCreate;
use Modules\Pos\Livewire\Floor\Show as FloorShow;

use Modules\Pos\Livewire\Payment\Lists as PaymentLists;

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

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/lists', OrderLists::class)->name('lists');
        Route::get('/{order}', OrderShow::class)->name('show');
    });

    // Sessions
    Route::prefix('sessions')->name('pos-sessions.')->group(function () {
        Route::get('/lists', PosSessionLists::class)->name('lists');
        // Route::get('/{session}', PosSessionShow::class)->name('show');
    });

    // Payments
    Route::prefix('order-payments')->name('order-payments.')->group(function () {
        Route::get('/lists', PaymentLists::class)->name('lists');
    });

    // Floor
    Route::prefix('floors')->name('pos-floors.')->group(function () {
        Route::get('/lists', FloorLists::class)->name('lists');
        Route::get('/create', FloorCreate::class)->name('create');
        Route::get('/{floor}', FloorShow::class)->name('show');
    });

});
