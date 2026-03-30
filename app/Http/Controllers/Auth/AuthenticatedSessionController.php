<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Flujo de inicio y cierre de sesión (formulario login, attempt, logout e invalidación de sesión).
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra la vista del formulario de acceso.
     */
    public function create(): View
    {
        return view('login');
    }

    /**
     * Valida email/contraseña, intenta autenticar, regenera la sesión y redirige al dashboard.
     *
     * @throws ValidationException Si las credenciales no son válidas.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.index'));
    }

    /**
     * Cierra sesión, invalida la sesión actual y regenera el token CSRF.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
