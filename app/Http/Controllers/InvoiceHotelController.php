<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreinvoiceRequest;
use App\Models\Invoice;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceHotelController extends Controller
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
            return $this->service->listInvoice('hotel');
        }
        return view('pages.invoice-hotel');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $listEmail = User::where('is_admin', 0)->pluck('email', 'id');
        return view('pages.modal.invoiceHotelModal', compact('listEmail'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreinvoiceRequest $request)
    {
        $isSuccess = $this->service->store($request->validated());
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice_hotel)
    {
        return $this->service->userById($invoice_hotel);
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
    public function destroy($invoice_hotel)
    {
        $isSuccess = Invoice::find($invoice_hotel)->delete();
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed') . ' to remove data'
            ]
        ]);
    }
}
