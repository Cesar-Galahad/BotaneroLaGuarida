<?php

namespace App\Http\Controllers;

use App\Models\Pedido;

class CocineroController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::where('estado', 'abierto')
                         ->with(['mesa', 'detalles.producto'])
                         ->orderBy('fecha')
                         ->get();

        return view('Cocina.pedidos', compact('pedidos'));
    }

    public function actualizar()
    {
        $pedidos = Pedido::where('estado', 'abierto')
                         ->with(['mesa', 'detalles.producto'])
                         ->orderBy('fecha')
                         ->get();

        return view('Cocina.partials.ordenes', compact('pedidos'));
    }

    public function marcarLista(Pedido $pedido)
    {
        // Por ahora solo confirma, en el futuro puede cambiar estado
        return response()->json(['ok' => true]);
    }
}