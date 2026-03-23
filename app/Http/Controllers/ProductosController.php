<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ProductoPrecio;
use App\Models\Tamanio;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    private function setEmpleadoSession()
    {
        $id = auth('empleado')->id();
        DB::statement("SET @empleado_id = ?", [$id]);
    }

    public function index(Request $request)
    {
        $rol = Auth::guard('empleado')->user()->rol->nombre ?? '';

        $query = $rol === 'Administrador'
            ? Producto::with(['categoria', 'precios'])
            : Producto::with(['categoria', 'precios'])->where('estado', 'activo');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        if ($rol === 'Administrador' && $request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $productos  = $query->orderBy('nombre')->paginate(12)->withQueryString();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('Productos.listado', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $tamanios   = Tamanio::orderBy('cantidad')->get();
        return view('Productos.crear', compact('categorias', 'tamanios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'               => 'required|string|max:150',
            'descripcion'          => 'nullable|string',
            'precio_base'          => 'nullable|numeric|min:0',
            'existencia'           => 'required|integer|min:0',
            'categoria_id'         => 'required|exists:categorias,id',
            'imagen'               => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'precios.*.tamanio_id' => 'required_with:precios|exists:tamanios,id',
            'precios.*.precio'     => 'required_with:precios|numeric|min:0.01',
        ]);

        $datos = $request->only(['nombre', 'descripcion', 'precio_base', 'existencia', 'categoria_id']);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $this->setEmpleadoSession();
        $producto = Producto::create($datos);

        if ($request->filled('precios')) {
            foreach ($request->precios as $precio) {
                if (!empty($precio['tamanio_id']) && isset($precio['precio'])) {
                    $producto->precios()->create([
                        'tamanio_id' => $precio['tamanio_id'],
                        'precio'     => $precio['precio'],
                    ]);
                }
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $tamanios   = Tamanio::orderBy('cantidad')->get();
        return view('Productos.crear', compact('producto', 'categorias', 'tamanios'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'               => 'required|string|max:150',
            'descripcion'          => 'nullable|string',
            'precio_base'          => 'nullable|numeric|min:0',
            'existencia'           => 'required|integer|min:0',
            'categoria_id'         => 'required|exists:categorias,id',
            'imagen'               => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'precios.*.tamanio_id' => 'required_with:precios|exists:tamanios,id',
            'precios.*.precio'     => 'required_with:precios|numeric|min:0.01',
        ]);

        $datos = $request->only(['nombre', 'descripcion', 'precio_base', 'existencia', 'categoria_id']);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $this->setEmpleadoSession();
        $producto->update($datos);

        $producto->precios()->delete();
        if ($request->filled('precios')) {
            foreach ($request->precios as $precio) {
                if (!empty($precio['tamanio_id']) && isset($precio['precio'])) {
                    $producto->precios()->create([
                        'tamanio_id' => $precio['tamanio_id'],
                        'precio'     => $precio['precio'],
                    ]);
                }
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $this->setEmpleadoSession();
        $nuevoEstado = $producto->estado === 'activo' ? 'inactivo' : 'activo';
        $producto->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado === 'inactivo' ? 'Producto desactivado.' : 'Producto activado.';
        return redirect()->route('productos.index')->with('success', $mensaje);
    }
}