<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriasController extends Controller
{
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('Categorias.listado', compact('categorias'));
    }

    public function create()
    {
        return view('Categorias.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,png', 'max:2048'],
        ]);

        $datos = $request->only('nombre');

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('categorias', 'public');
        }

        Categoria::create($datos);

        return redirect()->route('categorias.index')
                        ->with('success', 'Categoría registrada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('Categorias.crear', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $datos = $request->only('nombre');

        if ($request->hasFile('imagen')) {
            if ($categoria->imagen) {
                Storage::disk('public')->delete($categoria->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('categorias', 'public');
        }

        $categoria->update($datos);

        return redirect()->route('categorias.index')
                        ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $nuevoEstado = $categoria->estado === 'activo' ? 'inactivo' : 'activo';
        $categoria->update(['estado' => $nuevoEstado]);
        $mensaje = $nuevoEstado === 'inactivo' ? 'Categoría desactivada.' : 'Categoría activada.';
        return redirect()->route('categorias.index')->with('success', $mensaje);
    }
}
