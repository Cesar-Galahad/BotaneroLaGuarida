<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MesasController extends Controller
{
    public function index()
    {
        $mesas = Mesa::with('pedidoAbierto')->orderBy('numero')->get();
        return view('Mesas.listado', compact('mesas'));
    }

    public function seleccionar(Mesa $mesa)
    {
        // Si ya tiene pedido abierto, ir directo al POS
        $pedido = $mesa->pedidoAbierto;

        if ($pedido) {
            return redirect()->route('pos', $pedido);
        }

        // Si está libre, crear pedido nuevo y marcar mesa como ocupada
        $pedido = Pedido::create([
            'fecha'       => now(),
            'estado'      => 'abierto',
            'empleado_id' => Auth::guard('empleado')->id(),
            'mesa_id'     => $mesa->id,
        ]);

        $mesa->update(['estado' => 'ocupada']);

        return redirect()->route('pos', $pedido);
    }
}