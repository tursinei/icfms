<?php

namespace App\Http\Controllers;

use App\Services\AbstractService;
use Illuminate\Http\Request;

class AdminAbstractsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $service = new AbstractService();
            return $service->listAbstracts();
        }
        return view('pages.admin-abstracts');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $service = new AbstractService();
        $service->abstractExcel();
    }
 
}
