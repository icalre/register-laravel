<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;

class ForgotPasswordController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show form password recovery.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(EmailRequest $request)
    {

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if($response == \Password::RESET_LINK_SENT)
        {
            return back()->with('status', trans($response));
        }else{
            return back()->withErrors(
                ['email' => trans($response)]
            );
        }

    }

    public function broker()
    {
        return \Password::broker();
    }

}
