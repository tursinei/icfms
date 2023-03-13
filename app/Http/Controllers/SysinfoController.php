<?php

namespace App\Http\Controllers;

use App\Models\Sysinfo;
use App\Http\Requests\UpdateSysinfoRequest;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SysinfoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Sysinfo::all()->pluck('value','key');
        return view('pages.admin-setting',compact('data'));
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $service = new SettingService();
        $store = $service->store($request);
        if(isset($store['message'])){
            return response()->json($store,500);
        }
        return response()->json(['message' => 'Data has been saved']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sysinfo  $sysinfo
     * @return \Illuminate\Http\Response
     */
    public function show(Sysinfo $sysinfo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sysinfo  $sysinfo
     * @return \Illuminate\Http\Response
     */
    public function edit(Sysinfo $sysinfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSysinfoRequest  $request
     * @param  \App\Models\Sysinfo  $sysinfo
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSysinfoRequest $request, Sysinfo $sysinfo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sysinfo  $sysinfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sysinfo $sysinfo)
    {
        //
    }
}
