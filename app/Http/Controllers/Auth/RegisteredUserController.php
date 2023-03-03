<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Jobs\EmailRegistrationJob;
use App\Mail\RegistrationMail;
use App\Models\Country;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\AbstractService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $roles  = array_combine(AbstractService::ROLES,AbstractService::ROLES);
        $roles  = array_map(function(string $value){
            return ucfirst($value);
        }, $roles);

        return view('front.registration', compact('negara', 'afiliations','roles')); //'auth.register'
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request)
    {
        $request->validated();

        $name = implode(' ', [$request->firstname, $request->midlename, $request->lastname]);
        $data = [
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        DB::beginTransaction();
        try {
            $user = User::create($data);

            $data['password'] = $request->password;

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
                'mobilenumber'  => $request->mobilenumber,
                'presentation'  => $request->presentation
            ]);
            DB::commit();
            Auth::login($user);
            event(new Registered($user));
            // Mail::to($data['email'])->send(new RegistrationMail($data));
        } catch (\Exception $ex) {
            DB::rollBack();
            return redirect()->route('login')->with('error', 'Failed to Sign Up. <b>Error :'.$ex->getMessage().'</b>');
        }
        // dispatch(new EmailRegistrationJob($data)); // add to job
        // Artisan::call('queue:work');
        return redirect()->route('verification.notice');//->with('success', 'Please Sign In with data you have registered');
    }

}
