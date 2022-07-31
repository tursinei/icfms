<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\AbstractFile;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $service = new UserService();
            return $service->listUser(0);
        }
        return view('pages.participants');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $title = 'Change Password';
        return view('pages.modal.passwordModal',compact('user', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $service = new UserService();
        $isSuccess = $service->simpanAdmin($request);
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed') . ' to store data'
            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        $title = 'Download Abstract and Full Paper';
        $abstract = AbstractFile::leftJoin('full_paper', fn($join)=> $join->on('full_paper.abstract_id','abstract_file.abstract_id'))
                ->where('abstract_file.user_id', $user)->get(['abstract_file.abstract_id','abstract_title', 'full_paper.title','paper_id']);
        return view('pages.modal.downloadFileModal', compact('title', 'abstract'));
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
    public function changePass(ChangePasswordRequest $request)
    {
        $service = new UserService();
        $isSuccess = $service->changePass($request);
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess > 0 ? 'Success' : 'Failed') . ' change password'
            ]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $isSuccess = $user->delete();
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed') . ' to remove data'
            ]
        ], 200);
    }
}
