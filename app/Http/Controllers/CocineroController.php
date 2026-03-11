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
}