<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\PaymentNotifService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PaymentNotifController extends Controller
{
    private PaymentNotifService $service;

    public function __construct(PaymentNotifService $serviceParam)
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
            return $this->service->listPayment();
        }

        if(Session::get('icfms_tipe_login') == IS_MEMBER){
            return view('pages.payment-notif-user');
        }

        return view('pages.payment-notif');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $invoices = $this->service->getInvoices();
        return view('pages.modal.paymentNotifModal', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $isSuccess =$this->service->store($request);
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
     * @return \Illuminate\Http\Response
     */
    public function show($payment_notification)
    {
        $invoices = $this->service->getInvoices();
        return view('pages.modal.paymentNotifModal',compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentNotif  $paymentInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $payment_notification)
    {
        return $payment_notification;
    }

    public function downloadReceipt($invoiceId)
    {
        return $this->service->generateTemplate($invoiceId, true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentInvoice  $paymentInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentInvoice $paymentInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentInvoice  $paymentInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($payment_notification)
    {
        $isSuccess = $this->service->delete($payment_notification);
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

    public function storePayment(Request $request)
    {
        $isSuccess = $this->service->storePayment($request);
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed') . ' saved data'
            ]
        ]);
    }

    public function konfirmPayment(Request $request)
    {
        $isSuccess = $this->service->storeKonfirmasi($request);
        $msg = $request->input('is_confirm') ? 'Payment Confirmed' : 'Payment Rejected';
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed') . ' '.$msg
            ]
        ]);
    }

    public function handleNotifPayment(Request $request)
    {
        return $this->service->handleNotif($request);
    }
}
