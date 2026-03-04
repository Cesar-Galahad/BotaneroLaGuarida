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
        $productos = Producto::where('estado', 'activo')->orderBy('nombre')->get();
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
            'productos'    => ['nullable', 'array'],
            'productos.*'  => ['exists:productos,id'],
        ], [
            'nombre_p.required'     => 'El nombre es obligatorio.',
            'tipo.required'         => 'Selecciona el tipo de promoción.',
            'valor.required'        => 'El valor es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required'    => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha fin debe ser igual o posterior a la de inicio.',
        ]);

        $promocion = Promocion::create($request->only([
            'nombre_p', 'tipo', 'valor',
            'fecha_inicio', 'fecha_fin', 'estado',
        ]));

        if ($request->filled('productos')) {
            $promocion->productos()->sync($request->productos);
        }

        return redirect()->route('promociones.index')
                         ->with('success', 'Promoción creada correctamente.');
    }

    public function edit(Promocion $promocion)
    {
        $productos         = Producto::where('estado', 'activo')->orderBy('nombre')->get();
        $productosAsignados = $promocion->productos->pluck('id')->toArray();
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
            'productos'    => ['nullable', 'array'],
            'productos.*'  => ['exists:productos,id'],
        ]);

        $promocion->update($request->only([
            'nombre_p', 'tipo', 'valor',
            'fecha_inicio', 'fecha_fin', 'estado',
        ]));

        $promocion->productos()->sync($request->productos ?? []);

        return redirect()->route('promociones.index')
                         ->with('success', 'Promoción actualizada correctamente.');
    }

    public function destroy(Promocion $promocion)
    {
        $promocion->productos()->detach();
        $promocion->delete();

        return redirect()->route('promociones.index')
                         ->with('success', 'Promoción eliminada correctamente.');
    }
}