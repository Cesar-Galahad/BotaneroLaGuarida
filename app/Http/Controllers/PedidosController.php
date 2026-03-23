<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\DetallePedido;
use App\Models\ProductoCanje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidosController extends Controller
{
    public function pos(Pedido $pedido)
    {
        $hoy = now()->toDateString();

        $categorias = Categoria::orderBy('nombre')->get();

        $productos = Producto::where('estado', 'activo')
                        ->with(['precios.tamanio', 'promociones' => function($q) use ($hoy) {
                            $q->where('estado', 'activa')
                              ->where('fecha_inicio', '<=', $hoy)
                              ->where('fecha_fin',    '>=', $hoy);
                        }])
                        ->orderBy('nombre')
                        ->get();

        $promociones = \App\Models\Promocion::where('estado', 'activa')
                        ->where('fecha_inicio', '<=', $hoy)
                        ->where('fecha_fin',    '>=', $hoy)
                        ->with(['productos.precios.tamanio'])
                        ->get();

        $detalles = $pedido->detalles()->with(['producto', 'tamanio'])->get();

        $canjes = ProductoCanje::where('estado', 'activo')
                        ->with(['producto', 'tamanio'])
                        ->get();

        return view('Pedidos.pos', compact(
            'pedido', 'categorias', 'productos',
            'promociones', 'detalles', 'canjes'
        ));
    }

    public function index()
    {
        $pedidos = Pedido::with(['mesa', 'empleado'])
                         ->where('estado', 'abierto')
                         ->orderBy('fecha', 'desc')
                         ->get();

        return view('Pedidos.listado', compact('pedidos'));
    }

    public function agregarProducto(Request $request, Pedido $pedido)
    {
        try {
            $request->validate([
                'producto_id'  => ['required', 'exists:productos,id'],
                'cantidad'     => ['required', 'integer', 'min:1'],
                'promocion_id' => ['nullable', 'exists:promociones,id'],
                'precio'       => ['nullable', 'numeric', 'min:0'],
                'tamanio_id'   => ['nullable', 'exists:tamanios,id'],
            ]);

            $precioUnitario = $request->precio ?? Producto::findOrFail($request->producto_id)->precio_base;
            $descuento = 0;

            if ($request->filled('promocion_id')) {
                $hoy = now()->toDateString();
                $promocion = \App\Models\Promocion::where('id', $request->promocion_id)
                    ->where('estado', 'activa')
                    ->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy)
                    ->first();

                if ($promocion) {
                    $nombrePromo = strtolower($promocion->nombre_p);
                    if (str_contains($nombrePromo, '2x1')) {
                        $descuento = $precioUnitario;
                    } elseif (str_contains($nombrePromo, '3x2')) {
                        $descuento = $precioUnitario;
                    } else {
                        $descuento = $promocion->tipo === 'porcentaje'
                            ? round($precioUnitario * $promocion->valor / 100, 2)
                            : min($promocion->valor, $precioUnitario);
                    }
                }
            }

            $detalle = DetallePedido::where('pedido_id', $pedido->id)
                ->where('producto_id', $request->producto_id)
                ->where('tamanio_id', $request->tamanio_id ?? null)
                ->first();

            if ($detalle) {
                $detalle->update([
                    'cantidad'           => $detalle->cantidad + $request->cantidad,
                    'descuento_aplicado' => $descuento,
                ]);
            } else {
                DetallePedido::create([
                    'pedido_id'          => $pedido->id,
                    'producto_id'        => $request->producto_id,
                    'cantidad'           => $request->cantidad,
                    'precio_unitario'    => $precioUnitario,
                    'descuento_aplicado' => $descuento,
                    'tamanio_id'         => $request->tamanio_id ?? null,
                ]);
            }

            return response()->json(['ok' => true, 'descuento' => $descuento]);

        } catch (\Throwable $e) {
            return response()->json([
                'ok'    => false,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => basename($e->getFile()),
            ], 500);
        }
    }

    public function eliminarProducto(Request $request, Pedido $pedido)
    {
        DetallePedido::where('pedido_id',   $pedido->id)
                     ->where('producto_id', $request->producto_id)
                     ->where('tamanio_id',  $request->tamanio_id ?? null)
                     ->delete();

        return response()->json(['ok' => true]);
    }

    public function actualizarCantidad(Request $request, Pedido $pedido)
    {
        DetallePedido::where('pedido_id',   $pedido->id)
                     ->where('producto_id', $request->producto_id)
                     ->where('tamanio_id',  $request->tamanio_id ?? null)
                     ->update(['cantidad'  => $request->cantidad]);

        return response()->json(['ok' => true]);
    }

    public function cerrar(Request $request, Pedido $pedido)
    {
        $request->validate([
            'metodo_pago' => ['required', 'in:efectivo,tarjeta,transferencia,otro'],
        ], [
            'metodo_pago.required' => 'Selecciona un método de pago.',
        ]);

        $pedido->load('detalles.producto');

        $total = $pedido->detalles->sum(fn($d) => $d->subtotal);

        if ($total <= 0) {
            return back()->withErrors(['metodo_pago' => 'El total debe ser mayor a $0.']);
        }

        \App\Models\Pago::create([
            'pedido_id'   => $pedido->id,
            'metodo_pago' => $request->metodo_pago,
            'monto'       => $total,
        ]);

        // Descontar stock de cada producto
        foreach ($pedido->detalles as $detalle) {
            $detalle->producto->decrement('existencia', $detalle->cantidad);
        }

        $pedido->refresh();
        if ($pedido->cliente_id) {
            $puntos = (int) round($total * 0.05);
            $pedido->cliente->increment('puntos', $puntos);
        }

        $pedidoId = $pedido->id;
        $pedido->update(['estado' => 'cerrado']);
        $pedido->mesa->update(['estado' => 'libre']);

        return redirect()->route('mesas.index')
                        ->with('success', 'Cuenta cerrada. <a href="' . route('pedidos.ticket', $pedidoId) . '" target="_blank" class="font-bold underline">Ver ticket</a>');
    }

    public function cancelar(Pedido $pedido)
    {
        $pedido->update(['estado' => 'cancelado']);
        $pedido->mesa->update(['estado' => 'libre']);

        return redirect()->route('mesas.index')
                         ->with('success', 'Pedido cancelado.');
    }

    public function asignarCliente(Request $request, Pedido $pedido)
    {
        $pedido->update(['cliente_id' => $request->cliente_id]);
        return response()->json(['ok' => true]);
    }

    public function ticket(Pedido $pedido)
    {
        $pedido->load(['mesa', 'empleado', 'cliente', 'detalles.producto', 'detalles.tamanio', 'pagos']);
        return view('Pedidos.ticket', compact('pedido'));
    }

    public function canjearProducto(Request $request, Pedido $pedido)
    {
        $canje   = ProductoCanje::with(['producto', 'tamanio'])->findOrFail($request->canje_id);
        $cliente = $pedido->cliente;

        if (!$cliente) {
            return response()->json(['ok' => false, 'error' => 'No hay cliente asignado.'], 422);
        }

        if ($cliente->puntos < $canje->puntos_costo) {
            return response()->json(['ok' => false, 'error' => 'Puntos insuficientes.'], 422);
        }

        $cliente->decrement('puntos', $canje->puntos_costo);

        DetallePedido::create([
            'pedido_id'          => $pedido->id,
            'producto_id'        => $canje->producto_id,
            'cantidad'           => 1,
            'tamanio_id'         => $canje->tamanio_id ?? null,
            'precio_unitario' => 0,
            'descuento_aplicado' => 0,
        ]);

        return response()->json([
            'ok'               => true,
            'puntos_restantes' => $cliente->puntos,
            'producto'         => $canje->producto->nombre,
            'producto_id'      => $canje->producto_id,        // ← agregar
            'tamanio_id'       => $canje->tamanio_id ?? null, // ← agregar
            'tamanio'          => $canje->tamanio ? $canje->tamanio->cantidad . ' ' . $canje->tamanio->unidad : null,
        ]);
    }
}