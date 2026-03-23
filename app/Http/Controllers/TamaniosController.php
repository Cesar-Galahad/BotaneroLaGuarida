<?php

namespace App\Http\Controllers;

use App\Models\Tamanio;
use Illuminate\Http\Request;

class TamaniosController extends Controller
{
    public function index()
    {
        $tamanios = Tamanio::orderBy('unidad')->orderBy('cantidad')->get();
        return view('Tamanios.listado', compact('tamanios'));
    }

    public function create()
    {
        return view('Tamanios.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cantidad' => ['required', 'integer', 'min:1', 'max:999'],
            'unidad'   => ['required', 'in:pz,litro,vaso'],
        ], [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer'  => 'La cantidad debe ser un número entero.',
            'cantidad.min'      => 'La cantidad mínima es 1.',
            'cantidad.max'      => 'La cantidad máxima es 999.',
            'unidad.required'   => 'La unidad es obligatoria.',
            'unidad.in'         => 'La unidad debe ser pz, litro o vaso.',
        ]);

        $existe = Tamanio::where('cantidad', $request->cantidad)
                         ->where('unidad', $request->unidad)
                         ->exists();

        if ($existe) {
            return back()->withInput()
                         ->withErrors(['cantidad' => 'Ya existe un tamaño con esa cantidad y unidad.']);
        }

        Tamanio::create($request->only(['cantidad', 'unidad']));

        return redirect()->route('tamanios.index')
                         ->with('success', 'Tamaño creado correctamente.');
    }

    public function edit(Tamanio $tamanio)
    {
        return view('Tamanios.crear', compact('tamanio'));
    }

    public function update(Request $request, Tamanio $tamanio)
    {
        $request->validate([
            'cantidad' => ['required', 'integer', 'min:1', 'max:999'],
            'unidad'   => ['required', 'in:pz,litro,vaso'],
        ], [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer'  => 'La cantidad debe ser un número entero.',
            'cantidad.min'      => 'La cantidad mínima es 1.',
            'cantidad.max'      => 'La cantidad máxima es 999.',
            'unidad.required'   => 'La unidad es obligatoria.',
            'unidad.in'         => 'La unidad debe ser pz, litro o vaso.',
        ]);

        $existe = Tamanio::where('cantidad', $request->cantidad)
                         ->where('unidad', $request->unidad)
                         ->where('id', '!=', $tamanio->id)
                         ->exists();

        if ($existe) {
            return back()->withInput()
                         ->withErrors(['cantidad' => 'Ya existe un tamaño con esa cantidad y unidad.']);
        }

        $tamanio->update($request->only(['cantidad', 'unidad']));

        return redirect()->route('tamanios.index')
                         ->with('success', 'Tamaño actualizado correctamente.');
    }

    public function destroy(Tamanio $tamanio)
    {
        $nuevoEstado = $tamanio->estado === 'activo' ? 'inactivo' : 'activo';
        $tamanio->update(['estado' => $nuevoEstado]);
        $mensaje = $nuevoEstado === 'inactivo' ? 'Tamaño desactivado.' : 'Tamaño activado.';
        return redirect()->route('tamanios.index')->with('success', $mensaje);
    }
}