<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditTrailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private readonly AuditTrailService $auditTrail) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $this->auditTrail->record('login', 'User signed in successfully.');

        if ($request->user()?->role?->name === 'IT Technician') {
            return $this->redirectTechnician($request);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * IT Technicians may only use the ICT workspace, so a remembered page outside it would end in a 403.
     */
    private function redirectTechnician(LoginRequest $request): RedirectResponse
    {
        $intendedPath = parse_url((string) $request->session()->get('url.intended'), PHP_URL_PATH) ?? '';

        if (! str_starts_with('/'.ltrim($intendedPath, '/'), '/technician/')) {
            $request->session()->forget('url.intended');
        }

        return redirect()->intended(route('technician.dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->auditTrail->record('logout', 'User signed out.');
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
