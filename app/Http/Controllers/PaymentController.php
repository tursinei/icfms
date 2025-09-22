<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\AbstractFile;
use App\Models\FullPaper;
use App\Models\Invoice;
use App\Models\Payments;
use App\Models\UserDetail;
use App\Services\PaymentService;
use Carbon\Carbon;
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
        if (session('icfms_tipe_login') == 1) {
            if ($request->ajax()) {
                $service = new PaymentService();
                return $service->listTable();
            }
            return view('pages.admin-payments');
        }
        $dateBetween = [
            Carbon::now()->startOfYear()->toDateString(),
            Carbon::now()->endOfYear()->toDateString()
        ];
        $users = UserDetail::find(Auth::user()->id);
        $totalAbstract = AbstractFile::where('user_id', Auth::user()->id)->count();
        $totalPaper = FullPaper::where('user_id', Auth::user()->id)->count();
        $payment = Payments::where('user_id', Auth::user()->id)->whereBetween('created_at', $dateBetween)->pluck('invoice_id','payment_id');
        $payments = array_flip(array_filter($payment->toArray()));
        // dd($payment, $payments);
        $invoices = Invoice::where('user_id', Auth::user()->id)->whereBetween('tgl_invoice', $dateBetween)->get();

        return view('pages.payment', compact('totalAbstract', 'totalPaper', 'users', 'payments', 'invoices'));
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
            'url'   => route('payment.show', ['payment' => $payment->payment_id, 'action' => 'view']),
            'id'    => $payment->payment_id,
            'message' => ($payment ? 'Success' : 'Failed') . ' to store data'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Payments $payment, $action)
    {
        $path = public_path($payment->file_path);
        if (File::exists($path)) {
            return $action == 'view' ? response()->file($path) : response()->download($path);
        } else {
            throw new Exception('File Not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Payments  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payments::find($id);
        $path = public_path($payment->file_path);
        if (File::exists($path)) {
            File::delete($path);
        }
        $isDelete = $payment->delete();
        return response()->json([
            'status' => $isDelete,
            'message' => ($isDelete ? 'Success' : 'Failed') . ' to delete data'
        ], 200);
    }
}
