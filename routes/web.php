<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\TarjetaCreditoController;
use App\Http\Controllers\GastoTarjetaController;

Route::get('/', function () {
    return redirect('/inicio');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/inicio', [HomeController::class, 'index'])->name('home');
    Route::resource('categorias', CategoriaController::class);
    Route::resource('ingresos', IngresoController::class);
    Route::resource('gastos', GastoController::class);
    Route::resource('tarjetas', TarjetaCreditoController::class)->names('tarjetas');
    // Rutas para gastos de tarjeta
    // 'index' y 'create' están anidadas porque dependen de una tarjeta específica
    Route::get('tarjetas/{tarjeta}/gastos', [GastoTarjetaController::class, 'index'])->name('gastos_tarjeta.index');
    Route::get('tarjetas/{tarjeta}/gastos/create', [GastoTarjetaController::class, 'create'])->name('gastos_tarjeta.create');
    Route::post('tarjetas/{tarjeta}/gastos', [GastoTarjetaController::class, 'store'])->name('gastos_tarjeta.store');

    // 'edit', 'update', 'destroy' no están anidadas porque operan sobre el gasto_padre_id
    Route::get('gastos_tarjeta/{gasto_padre_id}/edit', [GastoTarjetaController::class, 'edit'])->name('gastos_tarjeta.edit');
    Route::put('gastos_tarjeta/{gasto_padre_id}', [GastoTarjetaController::class, 'update'])->name('gastos_tarjeta.update');
    Route::delete('gastos_tarjeta/{gasto_padre_id}', [GastoTarjetaController::class, 'destroy'])->name('gastos_tarjeta.destroy');
});
