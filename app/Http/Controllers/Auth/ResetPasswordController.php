<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * Show form reset password.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset Password.
     */
    public function reset(ResetPasswordRequest $request)
    {

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = $this->broker()->reset(
            $credentials, function ($user, $password) {

                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));

                Auth::guard()->login($user);
            }
        );

        if($response == Password::RESET_LINK_SENT)
        {
            return back()->with('status', trans($response));
        }else{
            return back()
                ->withInput($request->only('email'))
                ->withErrors(
                ['email' => trans($response)]
            );
        }
    }

    public function broker()
    {
        return Password::broker();
    }


}
