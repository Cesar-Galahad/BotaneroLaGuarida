<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmpleadosController extends Controller
{
    // ── AUTH ───────────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::guard('empleado')->check()) {
            return redirect()->route('dashboard');
        }
        return view('Autentificacion.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo'     => ['required', 'email'],
            'contrasena' => ['required', 'string'],
        ], [
            'correo.required'     => 'El correo es obligatorio.',
            'correo.email'        => 'Ingresa un correo válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        $empleado = Empleado::where('correo', $request->correo)
                            ->where('estado', 'activo')
                            ->first();

        if (! $empleado || ! Hash::check($request->contrasena, $empleado->contrasena)) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'Credenciales incorrectas o cuenta inactiva.']);
        }

        Auth::guard('empleado')->login($empleado);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('empleado')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ── CRUD ───────────────────────────────────────────────────

    public function index()
    {
        $empleados = Empleado::with('rol')
                             ->orderBy('apellidop')
                             ->get();

        return view('Empleados.listado', compact('empleados'));
    }

    public function create()
    {
        $roles = Rol::where('estado', 'activo')->get();
        return view('Empleados.crear', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'     => ['required', 'string', 'max:100'],
            'apellidop'  => ['required', 'string', 'max:100'],
            'apellidom'  => ['nullable', 'string', 'max:100'],
            'correo'     => ['required', 'email', 'unique:empleados,correo'],
            'contrasena' => ['required', 'string', 'min:6'],
            'rol_id'     => ['required', 'exists:roles,id'],
            'estado'     => ['required', 'in:activo,inactivo'],
            'imagen'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $datos = [
            'nombre'     => $request->nombre,
            'apellidop'  => $request->apellidop,
            'apellidom'  => $request->apellidom,
            'correo'     => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
            'estado'     => $request->estado,
            'rol_id'     => $request->rol_id,
        ];

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('empleados', 'public');
        }

        Empleado::create($datos);

        return redirect()->route('empleados.index')
                        ->with('success', 'Empleado registrado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        $roles = Rol::where('estado', 'activo')->get();
        return view('Empleados.crear', compact('empleado', 'roles')); // misma vista
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre'     => ['required', 'string', 'max:100'],
            'apellidop'  => ['required', 'string', 'max:100'],
            'apellidom'  => ['nullable', 'string', 'max:100'],
            'correo'     => ['required', 'email', "unique:empleados,correo,{$empleado->id}"],
            'contrasena' => ['nullable', 'string', 'min:6'],
            'rol_id'     => ['required', 'exists:roles,id'],
            'estado'     => ['required', 'in:activo,inactivo'],
            'imagen'     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $datos = [
            'nombre'    => $request->nombre,
            'apellidop' => $request->apellidop,
            'apellidom' => $request->apellidom,
            'correo'    => $request->correo,
            'rol_id'    => $request->rol_id,
            'estado'    => $request->estado,
        ];

        if ($request->filled('contrasena')) {
            $datos['contrasena'] = Hash::make($request->contrasena);
        }

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($empleado->imagen) {
                Storage::disk('public')->delete($empleado->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('empleados', 'public');
        }

        $empleado->update($datos);

        return redirect()->route('empleados.index')
                        ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        // No borramos físicamente, solo inactivamos
        $empleado->update(['estado' => 'inactivo']);

        return redirect()->route('empleados.index')
                         ->with('success', 'Empleado dado de baja.');
    }
}