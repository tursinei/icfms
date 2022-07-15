<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\AbstractFile;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(session('icfms_tipe_login') == 1){
            if($request->ajax()){
                $service = new UserService();
                return $service->listUser(session('icfms_tipe_login'));
            }
            return view('dashboardadmin');
        }
        $abstract = AbstractFile::where('user_id', Auth::user()->id)->pluck('abstract_title', 'abstract_id')->toArray();
        return view('dashboard', compact('abstract'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        User::updateOrCreate(['id' => $request->input('id')], $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($dashboard)
    {
        $title = 'User Admin Form';
        $user = User::find($dashboard);
        return view('pages.modal.userModal', compact('title', 'user'));
    }

    public function getAbstracts($id)
    {
        $data = AbstractFile::leftJoin('full_paper as a',function($join){
                    $join->on('a.abstract_id','abstract_file.abstract_id');
                })->where('abstract_file.abstract_id',$id)->select(['abstract_title','authors',
                        'abstract_file.created_at as abstract_submision', 'a.created_at as paper_submision'])->first();
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
