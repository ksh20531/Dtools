<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){
        $input_email = $request->get('email');
        $input_password = $request->get('password');

        $user = User::where('email',$input_email)->first();

        if(!empty($user) && $input_email = $user->email){
            if(Hash::check($input_password, $user->password)){
                \Auth::login($user);
                return redirect('/');
            }else{
                return redirect('login')->with('error', 'Password is wrong.');
            }
        }else{
            return redirect('login')->with('error', 'User does not exist.');
        }
    }
}
