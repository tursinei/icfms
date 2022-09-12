<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\EmailRegistrationJob;
use App\Mail\RegistrationMail;
use App\Models\Country;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        $afiliations = UserDetail::affiliations();
        $afiliations = array_merge(['' => '-- Choose your affiliation --'], $afiliations);
        return view('front.registration',compact('negara','afiliations'));//'auth.register'
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
            'midlename' => ['nullable','string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:6'],
            'affiliation' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'phonenumber' => ['required', 'string', 'max:255'],
            'mobilenumber' => ['required', 'string', 'max:255'],
        ]);

        $name = implode(' ', [$request->firstname, $request->midlename, $request->lastname]);
        $data = [
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        $user = User::create($data);

        $data['password'] = $request->password;
        $data['affiliation'] = $request->affiliation;

        UserDetail::create([
            'user_id'   => $user->id,
            'title'     => $request->title,
            'firstname' => $request->firstname,
            'midlename' => $request->midlename,
            'lastname'  => $request->lastname,
            'affiliation'  => $request->affiliation,
            'address'   => $request->address,
            'country'   => $request->country,
            'secondemail'   => $request->secondemail,
            'phonenumber'   => $request->phonenumber,
            'mobilenumber'  => $request->mobilenumber
        ]);

        event(new Registered($user));
        Mail::to($data['email'])->send(new RegistrationMail($data));
        // dispatch(new EmailRegistrationJob($data)); // add to job
        // Artisan::call('queue:work');
        return redirect()->route('login')->with('success','Please Sign In with data you have registered');
    }
}
