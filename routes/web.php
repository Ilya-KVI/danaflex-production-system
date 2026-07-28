<?php

use App\Http\Controllers\ProductionLineController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductionOrderController;


Route::get('/', function () {

    return Inertia::render('Dashboard');

});


Route::get('/production-orders', [
    ProductionOrderController::class,
    'index'
]);


Route::get('/dashboard', [
    ProductionOrderController::class,
    'dashboard'
]);


Route::post('/production-orders/{id}/status', [
    ProductionOrderController::class,
    'updateStatus'
]);

Route::post('/production-lines/{id}/start', [
    ProductionLineController::class,
    'start'
]);

Route::post('/production-lines/{id}/stop', [
    ProductionLineController::class,
    'stop'
]);
