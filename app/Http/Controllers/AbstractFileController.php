<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAbstractFileRequest;
use App\Models\AbstractFile;
use App\Models\Mtopic;
use App\Services\AbstractService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AbstractFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $abstract = new AbstractService();
            return $abstract->listTable(Auth::user()->id);
        }
        return view('pages.abstract');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Abstract Submission Form';
        $topic = Mtopic::pluck('name', 'topic_id')->toArray();
        return view('pages.modal.abstractModal', compact('title', 'topic'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAbstractFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAbstractFileRequest $request)
    {
        $service = new AbstractService();
        $isSuccess = $service->simpan($request);
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
     * @param  \App\Models\AbstractFile  $abstractFile
     * @return \Illuminate\Http\Response
     */
    public function show(AbstractFile $abstract)
    {
        $path = public_path($abstract->file_path);
        if (File::exists($path)) {
            return response()->download($path, $abstract->file_name);
        } else {
            throw new Exception("File Not Found", 1);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AbstractFile  $abstractFile
     * @return \Illuminate\Http\Response
     */
    public function edit(AbstractFile $abstract)
    {
        $abstract->topic;
        // $abs = $abstract->toArray();
        return response()->json($abstract);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AbstractFile  $abstractFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(AbstractFile $abstract)
    {
        if (File::exists($abstract->file_path)) {
            File::delete($abstract->file_path);
        }
        $isSuccess = $abstract->delete();
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed') . ' to store data'
            ]
        ], 200);
    }
}
