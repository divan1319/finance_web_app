<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function loguear(Request $request)
    {
        $credenciales = $request->only('email', 'password');

        if (Auth::attempt($credenciales)) {
            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['auth' => 'Correo o contraseña incorrectos']);
    }
}