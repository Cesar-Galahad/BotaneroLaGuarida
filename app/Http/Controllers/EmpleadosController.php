<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

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

        // Verificar si el correo existe
        $empleado = Empleado::where('correo', $request->correo)->first();

        if (! $empleado) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'No encontramos una cuenta con ese correo.']);
        }

        // Verificar si está inactivo
        if ($empleado->estado === 'inactivo') {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'Tu cuenta está desactivada, contacta al administrador.']);
        }

        // Verificar contraseña
        if (! Hash::check($request->contrasena, $empleado->contrasena)) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['contrasena' => 'La contraseña es incorrecta.']);
        }

        Auth::guard('empleado')->login($empleado);
        $request->session()->regenerate();

        // Si es primer login, redirigir a cambio de contraseña
        if ($empleado->primer_login) {
            return redirect()->route('primer.login');
        }

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
    public function showPrimerLogin()
    {
        return view('Autentificacion.primer_login');
    }

    public function cambiarContrasena(Request $request)
    {
        $request->validate([
            'contrasena_nueva'    => ['required', 'string', 'min:6'],
            'contrasena_confirmar'=> ['required', 'same:contrasena_nueva'],
        ], [
            'contrasena_nueva.required'     => 'La nueva contraseña es obligatoria.',
            'contrasena_nueva.min'          => 'La contraseña debe tener al menos 6 caracteres.',
            'contrasena_confirmar.required' => 'Confirma tu nueva contraseña.',
            'contrasena_confirmar.same'     => 'Las contraseñas no coinciden.',
        ]);

        $empleado = Auth::guard('empleado')->user();

        $empleado->update([
            'contrasena'   => Hash::make($request->contrasena_nueva),
            'primer_login' => 0,
        ]);

        return redirect()->route('dashboard')
                        ->with('success', '¡Contraseña actualizada correctamente!');
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                            ->withErrors(['correo' => 'No se pudo autenticar con Google.']);
        }

        // Buscar empleado por google_id o por correo
        $empleado = Empleado::where('google_id', $googleUser->getId())
                            ->orWhere('correo', $googleUser->getEmail())
                            ->first();

        // Si no existe en la BD, no puede entrar
        if (! $empleado) {
            return redirect()->route('login')
                            ->withErrors(['correo' => 'Tu cuenta de Google no está registrada en el sistema.']);
        }

        // Si está inactivo
        if ($empleado->estado === 'inactivo') {
            return redirect()->route('login')
                            ->withErrors(['correo' => 'Tu cuenta está desactivada, contacta al administrador.']);
        }

        // Guardar google_id si es la primera vez que usa Google
        if (! $empleado->google_id) {
            $empleado->update(['google_id' => $googleUser->getId()]);
        }

        Auth::guard('empleado')->login($empleado);
        request()->session()->regenerate();

        if ($empleado->primer_login) {
            return redirect()->route('primer.login');
        }

        return redirect()->route('dashboard');
    }
}