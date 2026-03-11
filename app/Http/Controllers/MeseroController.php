<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Pedido;

class MeseroController extends Controller
{
    public function dashboard()
    {
        $mesasOcupadas = Mesa::where('estado', 'ocupada')->count();
        $mesasLibres   = Mesa::where('estado', 'libre')->count();
        $pedidosAbiertos = Pedido::where('estado', 'abierto')
                                 ->whereDate('fecha', today())
                                 ->count();

        return view('Mesero.dashboard', compact(
            'mesasOcupadas', 'mesasLibres', 'pedidosAbiertos'
        ));
    }
}