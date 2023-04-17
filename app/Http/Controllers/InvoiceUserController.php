<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class InvoiceUserController extends Controller
{
    private InvoiceService $service;

    public function __construct(InvoiceService $serviceParam)
    {
        $this->service = $serviceParam;
    }

    public function index()
    {
        $data = $this->service->getSnapData(Auth::user()->id);
        return view('pages.invoice-user', compact('data'));
    }


}
