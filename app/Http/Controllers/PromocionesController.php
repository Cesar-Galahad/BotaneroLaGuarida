<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Producto;
use Illuminate\Http\Request;

class PromocionesController extends Controller
{
    public function index()
    {
        $promociones = Promocion::withCount('productos')->orderBy('fecha_inicio', 'desc')->get();
        return view('Promociones.listado', compact('promociones'));
    }

    public function create()
    {
        $productos = Producto::with('precios.tamanio')->where('estado', 'activo')->orderBy('nombre')->get();
        return view('Promociones.crear', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_p'     => ['required', 'string', 'max:150'],
            'tipo'         => ['required', 'in:porcentaje,monto'],
            'valor'        => ['required', 'numeric', 'min:0'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'estado'       => ['required', 'in:activa,inactiva'],
        ]);

        $promocion = Promocion::create($request->only([
            'nombre_p', 'tipo', 'valor', 'fecha_inicio', 'fecha_fin', 'estado',
        ]));

        if ($request->filled('productos')) {
            foreach ($request->productos as $item) {
                if (!empty($item['id'])) {
                    $promocion->productos()->attach($item['id'], [
                        'tamanio_id' => !empty($item['tamanio_id']) ? $item['tamanio_id'] : null,
                    ]);
                }
            }
        }

        return redirect()->route('promociones.index')
                         ->with('success', 'Promoción creada correctamente.');
    }

    public function edit(Promocion $promocion)
    {
        $productos          = Producto::with('precios.tamanio')->orderBy('nombre')->get();
        $productosAsignados = $promocion->productos->map(fn($p) => [
            'id'         => $p->id,
            'tamanio_id' => $p->pivot->tamanio_id ? (string) $p->pivot->tamanio_id : '',
        ])->toArray();

        return view('Promociones.crear', compact('promocion', 'productos', 'productosAsignados'));
    }

    public function update(Request $request, Promocion $promocion)
    {
        $request->validate([
            'nombre_p'     => ['required', 'string', 'max:150'],
            'tipo'         => ['required', 'in:porcentaje,monto'],
            'valor'        => ['required', 'numeric', 'min:0'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'estado'       => ['required', 'in:activa,inactiva'],
        ]);

        $promocion->update($request->only([
            'nombre_p', 'tipo', 'valor', 'fecha_inicio', 'fecha_fin', 'estado',
        ]));

        $promocion->productos()->detach();

        if ($request->filled('productos')) {
            foreach ($request->productos as $item) {
                if (!empty($item['id'])) {
                    $promocion->productos()->attach($item['id'], [
                        'tamanio_id' => !empty($item['tamanio_id']) ? $item['tamanio_id'] : null,
                    ]);
                }
            }
        }

        return redirect()->route('promociones.index')
                         ->with('success', 'Promoción actualizada correctamente.');
    }

    public function destroy(Promocion $promocion)
    {
        $nuevoEstado = $promocion->estado === 'activa' ? 'inactiva' : 'activa';
        $promocion->update(['estado' => $nuevoEstado]);
        $mensaje = $nuevoEstado === 'inactiva' ? 'Promoción desactivada.' : 'Promoción activada.';
        return redirect()->route('promociones.index')->with('success', $mensaje);
    }
}