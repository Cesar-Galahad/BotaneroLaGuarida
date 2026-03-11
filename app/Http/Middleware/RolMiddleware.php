<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $empleado = Auth::guard('empleado')->user();

        if (! $empleado) {
            return redirect()->route('login');
        }

        $rolNombre = $empleado->rol->nombre ?? '';

        if (! in_array($rolNombre, $roles)) {
            // Redirigir a su dashboard correspondiente
            return redirect()->route($this->dashboardPorRol($rolNombre));
        }

        return $next($request);
    }

    private function dashboardPorRol(string $rol): string
    {
        return match($rol) {
            'Mesero'   => 'mesero.dashboard',
            'Cocinero' => 'cocina.pedidos',
            default    => 'dashboard',
        };
    }
}