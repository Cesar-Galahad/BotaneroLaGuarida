<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductosController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->orderBy('nombre')->get();
        return view('Productos.listado', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('Productos.crear', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string'],
            'precio_base'  => ['required', 'numeric', 'min:0'],
            'existencia'   => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'estado'       => ['required', 'in:activo,inactivo'],
            'imagen'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $datos = $request->only([
            'nombre', 'descripcion', 'precio_base',
            'existencia', 'estado', 'categoria_id',
        ]);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($datos);

        return redirect()->route('productos.index')
                        ->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('Productos.crear', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string'],
            'precio_base'  => ['required', 'numeric', 'min:0'],
            'existencia'   => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'estado'       => ['required', 'in:activo,inactivo'],
            'imagen'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $datos = $request->only([
            'nombre', 'descripcion', 'precio_base',
            'existencia', 'estado', 'categoria_id',
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($datos);

        return redirect()->route('productos.index')
                        ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')
                         ->with('success', 'Producto eliminado correctamente.');
    }
}