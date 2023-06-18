<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use Illuminate\Http\Request;
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

    public function index(Request $request)
    {
        if($request->ajax()){
            return $this->service->listInvoiceUser();
        }
        $setInvoice = $request->get('id') ?? false;
        return view('pages.invoices-list', compact('setInvoice'));
    }

    public function formInvoice($invoiceId)
    {
        $data = $this->service->getSnapData($invoiceId);
        return view('pages.invoice-user', compact('data'))->render();
    }
}
