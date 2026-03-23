<?php

namespace App\Http\Controllers;

use App\Models\ProductoCanje;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductosCanjeController extends Controller
{
    public function index()
    {
        $canjes = ProductoCanje::with(['producto.categoria', 'tamanio'])
                        ->orderBy('puntos_costo')
                        ->get();
        return view('ProductosCanje.listado', compact('canjes'));
    }

    public function create()
    {
        $productos = Producto::where('estado', 'activo')
                            ->with(['precios.tamanio'])
                            ->orderBy('nombre')
                            ->get();
        return view('ProductosCanje.crear', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id'  => ['required', 'exists:productos,id'],
            'tamanio_id'   => ['nullable', 'exists:tamanios,id'],
            'puntos_costo' => ['required', 'integer', 'min:1'],
        ], [
            'producto_id.required'  => 'Selecciona un producto.',
            'puntos_costo.required' => 'El costo en puntos es obligatorio.',
            'puntos_costo.min'      => 'El mínimo es 1 punto.',
        ]);

        $existe = ProductoCanje::where('producto_id', $request->producto_id)
                            ->where('tamanio_id', $request->tamanio_id ?? null)
                            ->exists();

        if ($existe) {
            return back()->withInput()
                        ->withErrors(['producto_id' => 'Ya existe ese producto/tamaño en el catálogo de canje.']);
        }

        ProductoCanje::create([
            'producto_id'  => $request->producto_id,
            'tamanio_id'   => $request->tamanio_id ?? null,
            'puntos_costo' => $request->puntos_costo,
            'estado'       => $request->estado ?? 'activo',
        ]);

        return redirect()->route('canjes.index')
                        ->with('success', 'Producto de canje registrado correctamente.');
    }

    public function edit(ProductoCanje $canje)
    {
        $productos = Producto::where('estado', 'activo')
                            ->with(['precios.tamanio'])
                            ->orderBy('nombre')
                            ->get();
        return view('ProductosCanje.crear', compact('canje', 'productos'));
    }

    public function update(Request $request, ProductoCanje $canje)
    {
        $request->validate([
            'producto_id'  => ['required', 'exists:productos,id'],
            'tamanio_id'   => ['nullable', 'exists:tamanios,id'],
            'puntos_costo' => ['required', 'integer', 'min:1'],
        ]);

        $canje->update([
            'producto_id'  => $request->producto_id,
            'tamanio_id'   => $request->tamanio_id ?? null,
            'puntos_costo' => $request->puntos_costo,
            'estado'       => $request->estado,
        ]);

        return redirect()->route('canjes.index')
                        ->with('success', 'Producto de canje actualizado correctamente.');
    }

    public function destroy(ProductoCanje $canje)
    {
        $nuevoEstado = $canje->estado === 'activo' ? 'inactivo' : 'activo';
        $canje->update(['estado' => $nuevoEstado]);
        $mensaje = $nuevoEstado === 'inactivo' ? 'Producto de canje desactivado.' : 'Producto de canje activado.';
        return redirect()->route('canjes.index')->with('success', $mensaje);
    }
}