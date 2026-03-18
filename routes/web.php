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
use App\Http\Controllers\MeseroController;
use App\Http\Controllers\CocineroController;
use App\Http\Controllers\BitacoraController;

// ── Auth ──────────────────────────────────────────────────────
Route::get('/', [EmpleadosController::class, 'showLogin'])->name('login');
Route::post('/login', [EmpleadosController::class, 'login'])->name('login.post');
Route::post('/logout', [EmpleadosController::class, 'logout'])->name('logout');
// Fuera de cualquier grupo de auth
Route::get('/auth/google',          [EmpleadosController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [EmpleadosController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ── Rutas protegidas ──────────────────────────────────────────
Route::middleware('auth:empleado')->group(function () {

    // Primer login
    Route::get('/primer-login',  [EmpleadosController::class, 'showPrimerLogin'])->name('primer.login');
    Route::post('/primer-login', [EmpleadosController::class, 'cambiarContrasena'])->name('primer.login.post');

    // ── Rutas de solo lectura (todos los roles) ──────────────
    Route::get('/productos',    [ProductosController::class, 'index'])->name('productos.index');
    Route::get('/categorias',   [CategoriasController::class, 'index'])->name('categorias.index');
    Route::get('/promociones',  [PromocionesController::class, 'index'])->name('promociones.index');
    Route::get('/clientes',     [ClientesController::class, 'index'])->name('clientes.index');
    Route::get('/mesas',        [MesasController::class, 'index'])->name('mesas.index');
    Route::get('/pedidos',      [PedidosController::class, 'index'])->name('pedidos.index');

    // ── Solo Administrador ────────────────────────────────────
    Route::middleware('rol:Administrador')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // CRUD empleados
        Route::get('/empleados',                   [EmpleadosController::class, 'index'])->name('empleados.index');
        Route::get('/empleados/crear',             [EmpleadosController::class, 'create'])->name('empleados.create');
        Route::post('/empleados',                  [EmpleadosController::class, 'store'])->name('empleados.store');
        Route::get('/empleados/{empleado}/editar', [EmpleadosController::class, 'edit'])->name('empleados.edit');
        Route::put('/empleados/{empleado}',        [EmpleadosController::class, 'update'])->name('empleados.update');
        Route::delete('/empleados/{empleado}',     [EmpleadosController::class, 'destroy'])->name('empleados.destroy');

        // CRUD categorias
        Route::get('/categorias/crear',               [CategoriasController::class, 'create'])->name('categorias.create');
        Route::post('/categorias',                    [CategoriasController::class, 'store'])->name('categorias.store');
        Route::get('/categorias/{categoria}/editar',  [CategoriasController::class, 'edit'])->name('categorias.edit');
        Route::put('/categorias/{categoria}',         [CategoriasController::class, 'update'])->name('categorias.update');
        Route::delete('/categorias/{categoria}',      [CategoriasController::class, 'destroy'])->name('categorias.destroy');

        // CRUD productos
        Route::get('/productos/crear',               [ProductosController::class, 'create'])->name('productos.create');
        Route::post('/productos',                    [ProductosController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar',   [ProductosController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}',          [ProductosController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}',       [ProductosController::class, 'destroy'])->name('productos.destroy');

        // CRUD clientes
        Route::get('/clientes/crear',              [ClientesController::class, 'create'])->name('clientes.create');
        Route::post('/clientes',                   [ClientesController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}/editar',   [ClientesController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}',          [ClientesController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{cliente}',       [ClientesController::class, 'destroy'])->name('clientes.destroy');

        // CRUD promociones
        Route::get('/promociones/crear',               [PromocionesController::class, 'create'])->name('promociones.create');
        Route::post('/promociones',                    [PromocionesController::class, 'store'])->name('promociones.store');
        Route::get('/promociones/{promocion}/editar',  [PromocionesController::class, 'edit'])->name('promociones.edit');
        Route::put('/promociones/{promocion}',         [PromocionesController::class, 'update'])->name('promociones.update');
        Route::delete('/promociones/{promocion}',      [PromocionesController::class, 'destroy'])->name('promociones.destroy');
        /*
        // Mesas y pedidos completos
        Route::get('/mesas/{mesa}/seleccionar',      [MesasController::class, 'seleccionar'])->name('mesas.seleccionar');
        Route::get('/pedidos/{pedido}/pos',          [PedidosController::class, 'pos'])->name('pos');
        Route::post('/pedidos/{pedido}/agregar',     [PedidosController::class, 'agregarProducto'])->name('pedidos.agregar');
        Route::patch('/pedidos/{pedido}/cantidad',   [PedidosController::class, 'actualizarCantidad'])->name('pedidos.cantidad');
        Route::delete('/pedidos/{pedido}/eliminar',  [PedidosController::class, 'eliminarProducto'])->name('pedidos.eliminar');
        Route::patch('/pedidos/{pedido}/cerrar',     [PedidosController::class, 'cerrar'])->name('pedidos.cerrar');
        Route::patch('/pedidos/{pedido}/cancelar',   [PedidosController::class, 'cancelar'])->name('pedidos.cancelar');
        */
    });

    // ── Mesero ────────────────────────────────────────────────
    Route::middleware('rol:Mesero')->group(function () {
        Route::get('/mesero/dashboard',             [MeseroController::class, 'dashboard'])->name('mesero.dashboard');
        Route::get('/mesas/{mesa}/seleccionar',     [MesasController::class, 'seleccionar'])->name('mesas.seleccionar');
        Route::get('/pedidos/{pedido}/pos',         [PedidosController::class, 'pos'])->name('pos');
        Route::post('/pedidos/{pedido}/agregar',    [PedidosController::class, 'agregarProducto'])->name('pedidos.agregar');
        Route::patch('/pedidos/{pedido}/cantidad',  [PedidosController::class, 'actualizarCantidad'])->name('pedidos.cantidad');
        Route::delete('/pedidos/{pedido}/eliminar', [PedidosController::class, 'eliminarProducto'])->name('pedidos.eliminar');
        Route::patch('/pedidos/{pedido}/cerrar',    [PedidosController::class, 'cerrar'])->name('pedidos.cerrar');
        Route::patch('/pedidos/{pedido}/cancelar',  [PedidosController::class, 'cancelar'])->name('pedidos.cancelar');
        Route::get('/clientes/buscar', [ClientesController::class, 'buscar'])->name('clientes.buscar');
        Route::patch('/pedidos/{pedido}/cliente', [PedidosController::class, 'asignarCliente'])->name('pedidos.cliente');
    });

    // ── Cocinero ──────────────────────────────────────────────
    Route::middleware('rol:Cocinero')->group(function () {
        Route::get('/cocina/pedidos', [CocineroController::class, 'index'])->name('cocina.pedidos');
        Route::get('/cocina/pedidos/actualizar', [CocineroController::class, 'actualizar'])->name('cocina.actualizar');
        Route::patch('/cocina/pedidos/{pedido}/lista', [CocineroController::class, 'marcarLista'])->name('cocina.lista');
    });

    // ── Bitácora ───────────────────────────
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
});