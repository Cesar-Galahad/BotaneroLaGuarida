<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ProductoPrecio;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    // ── Establece la variable de sesión MySQL para los triggers ──
    private function setEmpleadoSession()
    {
        $id = auth('empleado')->id();
        DB::statement("SET @empleado_id = ?", [$id]);
    }

    public function index()
    {
        $rol = Auth::guard('empleado')->user()->rol->nombre ?? '';

        $productos = $rol === 'Administrador'
            ? Producto::with('categoria')->get()
            : Producto::with('categoria')->where('estado', 'activo')->get();

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
            'nombre'           => 'required|string|max:150',
            'descripcion'      => 'nullable|string',
            'precio_base'      => 'nullable|numeric|min:0',
            'existencia'       => 'required|integer|min:0',
            'categoria_id'     => 'required|exists:categorias,id',
            'imagen'           => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'precios.*.nombre' => 'required_with:precios|string|max:50',
            'precios.*.precio' => 'required_with:precios|numeric|min:0',
        ]);

        $datos = $request->only(['nombre', 'descripcion', 'precio_base', 'existencia', 'categoria_id']);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $this->setEmpleadoSession(); // <-- bitácora: registra quién creó
        $producto = Producto::create($datos);

        // Guardar precios si los hay
        if ($request->filled('precios')) {
            foreach ($request->precios as $precio) {
                if (!empty($precio['nombre']) && isset($precio['precio'])) {
                    $producto->precios()->create($precio);
                }
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('Productos.crear', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'           => 'required|string|max:150',
            'descripcion'      => 'nullable|string',
            'precio_base'      => 'nullable|numeric|min:0',
            'existencia'       => 'required|integer|min:0',
            'categoria_id'     => 'required|exists:categorias,id',
            'imagen'           => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'precios.*.nombre' => 'required_with:precios|string|max:50',
            'precios.*.precio' => 'required_with:precios|numeric|min:0',
        ]);

        $datos = $request->only(['nombre', 'descripcion', 'precio_base', 'existencia', 'categoria_id']);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $this->setEmpleadoSession(); // <-- bitácora: registra quién actualizó
        $producto->update($datos);

        // Reemplazar precios
        $producto->precios()->delete();
        if ($request->filled('precios')) {
            foreach ($request->precios as $precio) {
                if (!empty($precio['nombre']) && isset($precio['precio'])) {
                    $producto->precios()->create($precio);
                }
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $this->setEmpleadoSession(); // <-- bitácora: registra quién cambió el estado
        $nuevoEstado = $producto->estado === 'activo' ? 'inactivo' : 'activo';
        $producto->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado === 'inactivo' ? 'Producto desactivado.' : 'Producto activado.';
        return redirect()->route('productos.index')->with('success', $mensaje);
    }
}
