<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentNotifRequest;
use App\Models\PaymentNotif;
use App\Services\PaymentNotifService;
use Illuminate\Http\Request;

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
    public function store(StorePaymentNotifRequest $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($payment_notification)
    {
        $payment = $this->service->getPaymentInvoice($payment_notification);
        $invoices = $this->service->getInvoices();
        return view('pages.modal.paymentNotifModal',compact('invoices', 'payment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentNotif  $paymentInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentNotif $paymentNotif)
    {
        //
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
    public function destroy(PaymentInvoice $paymentInvoice)
    {
        //
    }
}
