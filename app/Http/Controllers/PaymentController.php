<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\AbstractFile;
use App\Models\FullPaper;
use App\Models\Payments;
use App\Models\UserDetail;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(session('icfms_tipe_login') == 1){
            if($request->ajax()){
                $service = new PaymentService();
                return $service->listTable();
            }
            return view('pages.admin-payments');
        }
        $users = UserDetail::find(Auth::user()->id);
        $totalAbstract = AbstractFile::where('user_id', Auth::user()->id)->count();
        $totalPaper = FullPaper::where('user_id', Auth::user()->id)->count();
        $payment = Payments::where('user_id',Auth::user()->id)->first();
        $isFileUploaded = false;
        if($payment !== null){
            $isFileUploaded = File::exists($payment->file_path);
        }

        return view('pages.payment', compact('totalAbstract','totalPaper', 'users','payment', 'isFileUploaded'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $service = new PaymentService();
        return $service->paymentsInExcel();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request)
    {
        $service = new PaymentService();
        $payment = $service->simpan($request);
        return response()->json([
            'url' => route('payment.show', ['payment' => $payment->payment_id]),
            'message' => ($payment ? 'Success' : 'Failed') . ' to store data'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Payments $payment)
    {
        $path = public_path($payment->file_path);
        if(File::exists($path)){
            return response()->file($path);
        } else {
            throw new Exception('File Not Found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
    public function destroy($id)
    {
        //
    }
}
