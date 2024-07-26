<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BebidaController;
use App\Http\Controllers\IngredienteController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::get('/', [DashboardController::class, 'index']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/corte', [DashboardController::class, 'corte'])->name('corte');
    Route::get('/inventario', [DashboardController::class, 'inventario'])->name('inventario');
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);

    
    Route::post('/inventario', [InventarioController::class, 'store']);
    Route::put('/inventario/{id}', [InventarioController::class, 'update']);

    
    Route::post('/ingredientes', [IngredienteController::class, 'agregarIngrediente'])->name('ingredientes.agregar');
    Route::put('/ingredientes/{id}', [IngredienteController::class, 'editarIngrediente'])->name('ingredientes.editar');
    Route::delete('/ingredientes/{id}', [IngredienteController::class, 'eliminarIngrediente'])->name('ingredientes.eliminar');
    Route::post('/bebidas', [IngredienteController::class, 'agregarBebida'])->name('bebidas.agregar');
    Route::put('/bebidas/{id}', [IngredienteController::class, 'editarBebida'])->name('bebidas.editar');
    Route::delete('/bebidas/{id}', [IngredienteController::class, 'eliminarBebida'])->name('bebidas.eliminar');
});



