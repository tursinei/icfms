<?php

namespace App\Http\Controllers;

use App\Services\FullpaperService;
use Illuminate\Http\Request;

class AdminFullpaperController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $service = new FullpaperService();
            return $service->listFullpaper($request->year);
        }
        return view('pages.admin-fullpaper');
    }

    public function create(Request $request)
    {
        $service = new FullpaperService();
        return $service->fullPaperInExcel($request->year);
    }
}
