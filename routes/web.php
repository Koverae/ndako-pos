<?php

use Illuminate\Support\Facades\Route;
use Modules\Pos\Http\Controllers\PosController;
use Modules\Pos\Livewire\Pos\Lists as PosLists;
use Modules\Pos\Livewire\Pos\Create as PosCreate;
use Modules\Pos\Livewire\Pos\Show as PosShow;
use Modules\Pos\Livewire\Pos\Ui as PosUi;

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
    Route::get('/pos/ui/{pos}', PosUi::class)->name('pos.ui');
});
