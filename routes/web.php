<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\MesasController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PromocionesController;

// ── Auth ──────────────────────────────────────────────────────
Route::get('/', [EmpleadosController::class, 'showLogin'])->name('login');
Route::post('/login', [EmpleadosController::class, 'login'])->name('login.post');
Route::post('/logout', [EmpleadosController::class, 'logout'])->name('logout');

// ── Rutas protegidas ──────────────────────────────────────────
Route::middleware('auth:empleado')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //clientes

    Route::get('/clientes',                  [ClientesController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear',            [ClientesController::class, 'create'])->name('clientes.create');
    Route::post('/clientes',                 [ClientesController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/editar', [ClientesController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}',        [ClientesController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}',     [ClientesController::class, 'destroy'])->name('clientes.destroy');

    // Empleados
    Route::get('/empleados',                [EmpleadosController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/crear',          [EmpleadosController::class, 'create'])->name('empleados.create');
    Route::post('/empleados',               [EmpleadosController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{empleado}/editar', [EmpleadosController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{empleado}',     [EmpleadosController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{empleado}',  [EmpleadosController::class, 'destroy'])->name('empleados.destroy');

    //mesas
    Route::get('/mesas',                          [MesasController::class, 'index'])->name('mesas.index');
    Route::get('/mesas/{mesa}/seleccionar',       [MesasController::class, 'seleccionar'])->name('mesas.seleccionar');

    //pedidos
    Route::get('/pedidos',                        [PedidosController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}/pos',           [PedidosController::class, 'pos'])->name('pos');
    Route::post('/pedidos/{pedido}/agregar',      [PedidosController::class, 'agregarProducto'])->name('pedidos.agregar');
    Route::patch('/pedidos/{pedido}/cantidad',    [PedidosController::class, 'actualizarCantidad'])->name('pedidos.cantidad');
    Route::delete('/pedidos/{pedido}/eliminar',   [PedidosController::class, 'eliminarProducto'])->name('pedidos.eliminar');
    Route::patch('/pedidos/{pedido}/cerrar',      [PedidosController::class, 'cerrar'])->name('pedidos.cerrar');
    Route::patch('/pedidos/{pedido}/cancelar',    [PedidosController::class, 'cancelar'])->name('pedidos.cancelar');

    Route::get('/productos',                  [ProductosController::class, 'index'])->name('productos.index');
    Route::get('/productos/crear',            [ProductosController::class, 'create'])->name('productos.create');
    Route::post('/productos',                 [ProductosController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/editar',[ProductosController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}',       [ProductosController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}',    [ProductosController::class, 'destroy'])->name('productos.destroy');

   
    Route::get('/promociones',                     [PromocionesController::class, 'index'])->name('promociones.index');
    Route::get('/promociones/crear',               [PromocionesController::class, 'create'])->name('promociones.create');
    Route::post('/promociones',                    [PromocionesController::class, 'store'])->name('promociones.store');
    Route::get('/promociones/{promocion}/editar',  [PromocionesController::class, 'edit'])->name('promociones.edit');
    Route::put('/promociones/{promocion}',         [PromocionesController::class, 'update'])->name('promociones.update');
    Route::delete('/promociones/{promocion}',      [PromocionesController::class, 'destroy'])->name('promociones.destroy');


    // dentro del grupo middleware('auth:empleado')
    Route::get('/categorias',                    [CategoriasController::class, 'index'])->name('categorias.index');
    Route::get('/categorias/crear',              [CategoriasController::class, 'create'])->name('categorias.create');
    Route::post('/categorias',                   [CategoriasController::class, 'store'])->name('categorias.store');
    Route::get('/categorias/{categoria}/editar', [CategoriasController::class, 'edit'])->name('categorias.edit');
    Route::put('/categorias/{categoria}',        [CategoriasController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{categoria}',     [CategoriasController::class, 'destroy'])->name('categorias.destroy');
});