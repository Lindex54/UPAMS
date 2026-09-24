<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictItTechnicianWorkspace
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $role = $user?->role?->name;

        if ($request->routeIs('technician.*') && ! in_array($role, ['IT Technician', 'System Administrator'], true)) {
            abort(403, 'This ICT workspace is available only to IT Technicians and System Administrators.');
        }

        if ($role !== 'IT Technician') {
            return $next($request);
        }

        if ($request->routeIs('dashboard')) {
            return redirect()->route('technician.dashboard');
        }

        if (! $request->routeIs('technician.*', 'logout')) {
            abort(403, 'IT Technicians can only access the ICT workspace assigned to them.');
        }

        return $next($request);
    }
}
