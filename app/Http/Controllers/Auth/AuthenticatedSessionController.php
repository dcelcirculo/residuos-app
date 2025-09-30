<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controlador de Sesión Autenticada
 * ----------------------------------------------------------------------------
 * Gestiona:
 * - create(): muestra el formulario de login.
 * - store(): procesa la autenticación y redirige al dashboard.
 * - destroy(): cierra la sesión y redirige al inicio.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar la vista de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Procesar solicitud de autenticación.
     * Usa LoginRequest (Breeze) que valida y ejecuta $request->authenticate().
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Autentica según las credenciales enviadas
        $request->authenticate();

        // Regenera el ID de sesión para evitar fijación de sesión
        $request->session()->regenerate();

        // Redirige a la ruta pretendida o al dashboard por defecto
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Cerrar sesión autenticada.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Cierra sesión del guard web
        Auth::guard('web')->logout();

        // Invalida la sesión y regenera el token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Vuelve a la pantalla inicial (welcome)
        return redirect('/');
    }
}