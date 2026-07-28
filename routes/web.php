<?php

use App\Http\Controllers\ProductionLineController;
use App\Http\Controllers\ProductionOrderController;
use Illuminate\Support\Facades\Route;


// Главная

Route::get('/', function () {

    return redirect('/dashboard');

});




// Dashboard

Route::get('/dashboard', [
    ProductionOrderController::class,
    'dashboard'
])->name('dashboard');






// Production Orders


Route::get('/production-orders', [
    ProductionOrderController::class,
    'index'
])->name('production-orders.index');



Route::get('/production-orders/create', [
    ProductionOrderController::class,
    'create'
])->name('production-orders.create');



Route::post('/production-orders', [
    ProductionOrderController::class,
    'store'
])->name('production-orders.store');




// Edit

Route::get('/production-orders/{id}/edit', [
    ProductionOrderController::class,
    'edit'
])->name('production-orders.edit');




// Update

Route::put('/production-orders/{id}', [
    ProductionOrderController::class,
    'update'
])->name('production-orders.update');




// Show

Route::get('/production-orders/{id}', [
    ProductionOrderController::class,
    'show'
])->name('production-orders.show');




// Change status

Route::post('/production-orders/{id}/status', [
    ProductionOrderController::class,
    'updateStatus'
])->name('production-orders.status');




// Delete

Route::delete('/production-orders/{id}', [
    ProductionOrderController::class,
    'destroy'
])->name('production-orders.destroy');






// Production Lines


Route::post('/production-lines/{id}/start', [
    ProductionLineController::class,
    'start'
])->name('production-lines.start');



Route::post('/production-lines/{id}/stop', [
    ProductionLineController::class,
    'stop'
])->name('production-lines.stop');
