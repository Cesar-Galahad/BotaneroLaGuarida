<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\DetallePedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidosController extends Controller
{
    // Vista POS
    public function pos(Pedido $pedido)
    {
        $hoy = now()->toDateString();

        $categorias = Categoria::orderBy('nombre')->get();

        $productos = Producto::where('estado', 'activo')
                        ->with(['promociones' => function($q) use ($hoy) {
                            $q->where('estado', 'activa')
                            ->where('fecha_inicio', '<=', $hoy)
                            ->where('fecha_fin', '>=', $hoy);
                        }, 'precios'])
                        ->orderBy('nombre')
                        ->get();

        // Promociones activas y en fecha con sus productos
        $promociones = \App\Models\Promocion::where('estado', 'activa')
                        ->where('fecha_inicio', '<=', $hoy)
                        ->where('fecha_fin', '>=', $hoy)
                        ->with('productos')
                        ->get();

        $detalles = $pedido->detalles()->with('producto')->get();
        $productosJs = $productos->map(fn($p) => [
            'id'           => $p->id,
            'nombre'       => $p->nombre,
            'precio'       => (float) $p->precio_base,
            'categoria_id' => $p->categoria_id,
            'imagen'       => $p->imagen,
            'precios'      => $p->precios->map(fn($pr) => [
                'nombre' => $pr->nombre,
                'precio' => (float) $pr->precio,
            ])->values(),
        ])->values();

        return view('Pedidos.pos', compact('pedido', 'categorias', 'productos', 'promociones', 'detalles', 'productosJs'));
    }

    // Listado de pedidos abiertos
    public function index()
    {
        $pedidos = Pedido::with(['mesa', 'empleado'])
                         ->where('estado', 'abierto')
                         ->orderBy('fecha', 'desc')
                         ->get();

        return view('Pedidos.listado', compact('pedidos'));
    }

    // Agregar o actualizar producto en el pedido
    public function agregarProducto(Request $request, Pedido $pedido)
    {
        $request->validate([
            'producto_id'  => ['required', 'exists:productos,id'],
            'cantidad'     => ['required', 'integer', 'min:1'],
            'promocion_id' => ['nullable', 'exists:promociones,id'],
        ]);

        $producto  = Producto::findOrFail($request->producto_id);
        $descuento = 0;

        // Calcular descuento si viene con promoción
        if ($request->filled('promocion_id')) {
            $hoy = now()->toDateString();
            $promocion = $producto->promociones()
                                ->where('promociones.id', $request->promocion_id)
                                ->where('estado', 'activa')
                                ->where('fecha_inicio', '<=', $hoy)
                                ->where('fecha_fin', '>=', $hoy)
                                ->first();

            if ($promocion) {
                $descuento = $promocion->tipo === 'porcentaje'
                    ? round($producto->precio_base * $promocion->valor / 100, 2)
                    : min($promocion->valor, $producto->precio_base);
            }
        }

        $detalle = DetallePedido::where('pedido_id', $pedido->id)
                        ->where('producto_id', $producto->id)
                        ->where('tamano', $request->tamano ?? null)
                        ->first();

        if ($detalle) {
            $detalle->update([
                'cantidad'           => $detalle->cantidad + $request->cantidad,
                'descuento_aplicado' => $descuento,
            ]);
        } else {
            DetallePedido::create([
                'pedido_id'          => $pedido->id,
                'producto_id'        => $producto->id,
                'cantidad'           => $request->cantidad,
                'precio_unitario'    => $request->precio ?? $producto->precio_base,
                'descuento_aplicado' => $descuento,
                'tamano'             => $request->tamano ?? null,
            ]);
        }

        return response()->json(['ok' => true, 'descuento' => $descuento]);
    }

    // Eliminar producto del pedido
    public function eliminarProducto(Request $request, Pedido $pedido)
    {
        $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
        ]);

        DetallePedido::where('pedido_id', $pedido->id)
                    ->where('producto_id', $request->producto_id)
                    ->where('tamano', $request->tamano ?? null)
                    ->delete();

        return response()->json(['ok' => true]);
    }

    // Actualizar cantidad de un producto
    public function actualizarCantidad(Request $request, Pedido $pedido)
    {
        $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
            'cantidad'    => ['required', 'integer', 'min:1'],
        ]);

        DetallePedido::where('pedido_id', $pedido->id)
                    ->where('producto_id', $request->producto_id)
                    ->where('tamano', $request->tamano ?? null)
                    ->update(['cantidad' => $request->cantidad]);

        return response()->json(['ok' => true]);
    }

    // Cerrar cuenta
    public function cerrar(Request $request, Pedido $pedido)
    {
        $request->validate([
            'metodo_pago' => ['required', 'in:efectivo,tarjeta,transferencia,otro'],
        ], [
            'metodo_pago.required' => 'Selecciona un método de pago.',
        ]);

        $total = $pedido->detalles->sum(
            fn($d) => ($d->precio_unitario - $d->descuento_aplicado) * $d->cantidad
        );

        \App\Models\Pago::create([
            'pedido_id'   => $pedido->id,
            'metodo_pago' => $request->metodo_pago,
            'monto'       => $total,
        ]);

        // Sumar puntos al cliente (10% del total)
        $pedido->refresh(); // recargar el pedido desde la BD
        if ($pedido->cliente_id) {
            $puntos = (int) round($total * 0.05);
            $pedido->cliente->increment('puntos', $puntos);
        }

        $pedido->update(['estado' => 'cerrado']);
        $pedido->mesa->update(['estado' => 'libre']);

        return redirect()->route('mesas.index')
                        ->with('success', 'Cuenta cerrada correctamente.');
    }

    // Cancelar pedido
    public function cancelar(Pedido $pedido)
    {
        $pedido->update(['estado' => 'cancelado']);
        $pedido->mesa->update(['estado' => 'libre']);

        return redirect()->route('mesas.index')
                         ->with('success', 'Pedido cancelado.');
    }
    public function asignarCliente(Request $request, Pedido $pedido)
    {
        $request->validate([
            'cliente_id' => ['nullable', 'exists:clientes,id'],
        ]);

        $pedido->update(['cliente_id' => $request->cliente_id]);

        return response()->json(['ok' => true]);
    }
}