<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $negara = Country::all()->pluck('nicename')->toArray();
        return view('front.registration',compact('negara'));//'auth.register'
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'midlename' => ['string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:6'],
            'affiliation' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'phonenumber' => ['required', 'string', 'max:255'],
            'mobilenumber' => ['required', 'string', 'max:255'],
        ]);

        $name = implode(' ', [$request->firstname, $request->midlename, $request->lastname]);

        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        UserDetail::create([
            'user_id'   => $user->id,
            'title'     => $request->title,
            'firstname' => $request->firstname,
            'midlename' => $request->midlename,
            'lastname'  => $request->lastname,
            'afiliasi'  => $request->affiliation,
            'address'   => $request->address,
            'country'   => $request->country,
            'secondemail'   => $request->secondemail,
            'phonenumber'   => $request->phonenumber,
            'mobilenumber'  => $request->mobilenumber
        ]);

        event(new Registered($user));

        // Auth::login($user);

        return redirect()->route('login')->with('success','Please Sign In with data you have registered');
    }
}
