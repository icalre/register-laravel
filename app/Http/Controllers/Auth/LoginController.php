<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $redirectTo = '/home';

    use ThrottlesLogins, RedirectsUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Login de usuario
     */
    public function login(LoginForm $request)
    {

        //Si se intentar iniciar sesión muchas veces el login es bloqueado
        if ($this->hasTooManyLoginAttempts($request)) {

            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->only('name', 'password');

        if (Auth::guard()->attempt($credentials, $request->has('remember'))) {

            $request->session()->regenerate();

            $this->clearLoginAttempts($request);

            return redirect()->intended($this->redirectPath());
        }

        // Si el login falla aumenta el numero de intentos de login.
        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function username()
    {
        return 'email';
    }

    /**
     * Desconectar usuario.
     */
    public function logout(Request $request)
    {
        Auth::guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }



}
