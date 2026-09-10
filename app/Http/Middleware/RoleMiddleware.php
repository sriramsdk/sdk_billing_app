<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (! auth()->check()) {
            return redirect()->route('admin.login');
        }

        if (! auth()->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('employee.login')
                ->withErrors(['email' => 'Your account is inactive.']);
        }

        if (in_array('employee', $roles, true)
            && auth()->user()->employee
            && ! auth()->user()->employee->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('employee.login')
                ->withErrors(['email' => 'Your employee account is inactive.']);
        }

        if (! in_array(auth()->user()->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}