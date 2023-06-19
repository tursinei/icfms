<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\StoreinvoiceRequest;
use App\Http\Requests\UpdateinvoiceRequest;
use App\Models\User;
use App\Services\AbstractService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    private InvoiceService $service;

    public function __construct(InvoiceService $serviceParam)
    {
        $this->service = $serviceParam;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            return $this->service->listInvoice();
        }
        return view('pages.invoice-notif');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $listEmail = User::where('is_admin',0)->pluck('email','id');
        $roles  = array_combine(AbstractService::ROLES, AbstractService::ROLES);
        $roles  = array_map(function (string $value) {
            return ucfirst($value);
        }, $roles);
        return view('pages.modal.invoiceModal',compact('listEmail', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreinvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreinvoiceRequest $request)
    {
        $isSuccess = $this->service->store($request);
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed') . ' to store data'
            ]
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceNotif $invoice)
    {
        //
    }

    public function downloadInvoice($invoiceId)
    {
        return $this->service->generateTemplate($invoiceId, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice_notification)
    {
        return $this->service->userById($invoice_notification);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateinvoiceRequest  $request
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateinvoiceRequest $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice_notification)
    {
        $isSuccess = Invoice::find($invoice_notification)->delete();
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed') . ' to remove data'
            ]
        ]);
    }

    public function excelFile()
    {
        return $this->service->excelFile();
    }
}
