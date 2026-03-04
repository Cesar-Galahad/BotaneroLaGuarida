<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\DetallePedido;

class DashboardController extends Controller
{
    public function index()
    {
        // Total vendido hoy (pedidos cerrados)
        $totalHoy = Pedido::where('estado', 'cerrado')
            ->whereDate('fecha', today())
            ->with('detalles')
            ->get()
            ->sum(fn($p) => $p->detalles->sum(
                fn($d) => ($d->precio_unitario - $d->descuento_aplicado) * $d->cantidad
            ));

        // Pedidos abiertos hoy
        $pedidosAbiertos = Pedido::where('estado', 'abierto')
            ->whereDate('fecha', today())
            ->count();

        // Mesas ocupadas vs libres
        $mesasOcupadas = Mesa::where('estado', 'ocupada')->count();
        $mesasLibres   = Mesa::where('estado', 'libre')->count();

        // Productos con pocas existencias (10 o menos)
        $productosBajos = Producto::where('estado', 'activo')
            ->where('existencia', '<=', 10)
            ->orderBy('existencia')
            ->get();

        return view('Dashboard.listado', compact(
            'totalHoy',
            'pedidosAbiertos',
            'mesasOcupadas',
            'mesasLibres',
            'productosBajos'
        ));
    }
}