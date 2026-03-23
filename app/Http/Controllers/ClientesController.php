<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre',    'like', '%' . $request->buscar . '%')
                ->orWhere('apellidop', 'like', '%' . $request->buscar . '%')
                ->orWhere('telefono',  'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $clientes = $query->orderBy('apellidop')->paginate(15)->withQueryString();

        return view('Clientes.listado', compact('clientes'));
    }

    public function create()
    {
        return view('Clientes.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'apellidop' => ['required', 'string', 'max:100'],
            'apellidom' => ['nullable', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'estado'    => ['required', 'in:activo,inactivo'],
            'imagen'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $datos = $request->only(['nombre', 'apellidop', 'apellidom', 'telefono', 'estado']);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('clientes', 'public');
        }

        Cliente::create($datos);

        return redirect()->route('clientes.index')
                        ->with('success', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('Clientes.crear', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'apellidop' => ['required', 'string', 'max:100'],
            'apellidom' => ['nullable', 'string', 'max:100'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'estado'    => ['required', 'in:activo,inactivo'],
            'imagen'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $datos = $request->only(['nombre', 'apellidop', 'apellidom', 'telefono', 'estado']);

        if ($request->hasFile('imagen')) {
            if ($cliente->imagen) {
                Storage::disk('public')->delete($cliente->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('clientes', 'public');
        }

        $cliente->update($datos);

        return redirect()->route('clientes.index')
                        ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $nuevoEstado = $cliente->estado === 'activo' ? 'inactivo' : 'activo';
        $cliente->update(['estado' => $nuevoEstado]);
        $mensaje = $nuevoEstado === 'inactivo' ? 'Cliente desactivado.' : 'Cliente activado.';
        return redirect()->route('clientes.index')->with('success', $mensaje);
    }
    public function buscar(Request $request)
    {
        $clientes = Cliente::where('estado', 'activo')
            ->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->q . '%')
                ->orWhere('apellidop', 'like', '%' . $request->q . '%')
                ->orWhere('telefono', 'like', '%' . $request->q . '%');
            })
            ->limit(5)
            ->get(['id', 'nombre', 'apellidop', 'puntos', 'imagen']);

        return response()->json($clientes);
    }
}
