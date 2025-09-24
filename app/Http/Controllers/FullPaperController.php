<?php

namespace App\Http\Controllers;

use App\Models\FullPaper;
use App\Http\Requests\StorefullPaperRequest;
use App\Models\AbstractFile;
use App\Models\Sysinfo;
use App\Services\FullpaperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class FullPaperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $service = new FullpaperService();
            return $service->listTable(Auth::user()->id);
        }
        $setting = Sysinfo::where('tipe','batas')->pluck('value','key');
        return view('pages.fullpaper', compact('setting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Full Paper Submission Form';
        $abstract = AbstractFile::periode()->where('user_id', Auth::user()->id)->pluck('abstract_title', 'abstract_id')->toArray();
        $abstract[0] = '-- Choose Related Abstract --';
        ksort($abstract);
        return view('pages.modal.paperModal', compact('title', 'abstract'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorefullPaperRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorefullPaperRequest $request)
    {
        $service = new FullpaperService();
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
     * @param  \App\Models\fullPaper  $fullPaper
     * @return \Illuminate\Http\Response
     */
    public function show(FullPaper $fullpaper)
    {
        $path = public_path($fullpaper->file_path);
        if (File::exists($path)) {
            return response()->download($path, urlencode($fullpaper->file_name));
        } else {
            throw new Exception("File Not Found", 1);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\fullPaper  $fullPaper
     * @return \Illuminate\Http\Response
     */
    public function edit(FullPaper $fullPaper)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\fullPaper  $fullPaper
     * @return \Illuminate\Http\Response
     */
    public function destroy(FullPaper $fullpaper)
    {
        if(File::exists($fullpaper->file_path)){
            File::delete($fullpaper->file_path);
        }
        $isSuccess = $fullpaper->delete();
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed').' to remove data'
            ]
        ], 200);
    }
}
