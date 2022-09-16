<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonalRequest;
use App\Models\Country;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = UserDetail::find(Auth::user()->id);
        $country = Country::all()->pluck('nicename')->toArray();
        $affiliations = UserDetail::affiliations();
        $affiliations = array_merge(['' => '--Choose your affiliation--'], $affiliations);
        $isOtherAffiliation = (!array_search($user->affiliation, $affiliations) && ($user->affiliation != ''));

        return view('pages.personal', compact('user', 'country', 'affiliations', 'isOtherAffiliation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PersonalRequest $request)
    {
        $data = $request->validated();
        if($data['affiliation'] == 'Another'){
            $data['affiliation'] = $data['another_affiliation'];
            unset($data['another_affiliation']);
            // dd($data);
        }
        UserDetail::updateOrCreate(['user_id' => $request->input('user_id')],$data);
        $name = implode(' ', [$request->input('firstname'), $request->input('midlename'), $request->input('lastname')]);
        $user = User::find($request->input('user_id'));
        $user->name = $name;
        $user->save();
        return back()->with('success', 'Data has been updated');
    }
}
