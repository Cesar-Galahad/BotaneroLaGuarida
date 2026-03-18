<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientesController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('apellidop')->get();
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
        $cliente->update(['estado' => 'inactivo']);
        return redirect()->route('clientes.index')
                        ->with('success', 'Cliente desactivado correctamente.');
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
