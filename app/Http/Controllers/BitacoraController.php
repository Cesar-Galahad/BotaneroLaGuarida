<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BitacoraController extends Controller
{
    /**
     * Vista principal con pestañas: productos y pedidos
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'productos'); // pestaña activa por defecto

        // ── Bitácora Productos ──────────────────────────────
        $queryProductos = DB::table('bitacora')
            ->leftJoin('empleados', 'bitacora.empleado_id', '=', 'empleados.id')
            ->where('bitacora.tabla', 'productos')
            ->select(
                'bitacora.*',
                DB::raw("CONCAT(COALESCE(empleados.nombre,''), ' ', COALESCE(empleados.apellidop,'')) as empleado_nombre")
            )
            ->orderBy('bitacora.fecha', 'desc');

        if ($tab === 'productos') {
            if ($request->filled('operacion'))    $queryProductos->where('bitacora.operacion', $request->operacion);
            if ($request->filled('fecha_inicio')) $queryProductos->whereDate('bitacora.fecha', '>=', $request->fecha_inicio);
            if ($request->filled('fecha_fin'))    $queryProductos->whereDate('bitacora.fecha', '<=', $request->fecha_fin);
        }

        $registrosProductos = $queryProductos->paginate(12, ['*'], 'page_prod')->withQueryString();

        // ── Bitácora Pedidos ────────────────────────────────
        $queryPedidos = DB::table('bitacora')
            ->leftJoin('empleados', 'bitacora.empleado_id', '=', 'empleados.id')
            ->where('bitacora.tabla', 'ordenar_pedidos')
            ->select(
                'bitacora.*',
                DB::raw("CONCAT(COALESCE(empleados.nombre,''), ' ', COALESCE(empleados.apellidop,'')) as empleado_nombre")
            )
            ->orderBy('bitacora.fecha', 'desc');

        if ($tab === 'pedidos') {
            if ($request->filled('operacion'))    $queryPedidos->where('bitacora.operacion', $request->operacion);
            if ($request->filled('fecha_inicio')) $queryPedidos->whereDate('bitacora.fecha', '>=', $request->fecha_inicio);
            if ($request->filled('fecha_fin'))    $queryPedidos->whereDate('bitacora.fecha', '<=', $request->fecha_fin);
        }

        $registrosPedidos = $queryPedidos->paginate(12, ['*'], 'page_ped')->withQueryString();

        return view('Bitacora.index', compact('registrosProductos', 'registrosPedidos', 'tab'));
    }
}
