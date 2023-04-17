<?php

namespace App\Services;

use App\Http\Requests\StoreinvoiceRequest;
use App\Http\Requests\StorePaymentNotifRequest;
use App\Mail\InvoiceNotificationMail;
use App\Models\AbstractFile;
use App\Models\InvoiceNotif;
use App\Models\PaymentNotif;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;

class PaymentNotifService
{
    public function listPayment(){
        $data = PaymentNotif::join('invoice_notif AS i', 'i.invoice_id', 'payment_notif.invoice_id')
            ->join('users AS u', 'u.id', 'i.user_id')
            ->orderBy('created_at', 'DESC')
            ->get(['payment_notif.*','u.email','u.name','i.invoice_number']);
        return DataTables::of($data)->addColumn('actions', function($row){
            return '<a class="btn btn-success btn-xs" href="' . route('invoice.file',['invoiceId' => $row->paymentnotif_id]) . '"
                title="Download Invoice" target="_blank" ><i class="fa fa-download"></i></a>&nbsp;
                <button data-href="' . route('invoice-notification.destroy', ['invoice_notification' => $row->paymentnotif_id]) . '"
                data-id="' . $row->paymentnotif_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Paper "><i class="fa fa-trash-o"></i></button>';
        })->addColumn('tanggal', function($row){
            return date('d-m-Y', strtotime($row->created_at));
        })->addColumn('title', function($row){
            return  $row->attribut['title'];
        })->addColumn('fullname', function($row){
            return  $row->attribut['fullname'];
        })->addColumn('affiliation', function($row){
            return  $row->attribut['affiliation'];
        })->addColumn('country', function($row){
            return  $row->attribut['country'];
        })->addColumn('prefnominal', function($row){
            return  $row->currency.' '.$row->nominal;
        })->rawColumns(['actions','title','fullname','affiliation','country', 'prefnominal'])->make(true);
    }

    public function userById($id)
    {
        $user = User::join('users_details as ud', 'users.id', 'ud.user_id')->where('id', $id)
                    ->get(['title','name','affiliation','country'])->first();
        $abstract = AbstractFile::where('user_id', $user->user_id)->get(['presentation','abstract_title']);
        $roles = $titles = [];
        foreach ($abstract as $value) {
            $roles[] = $value->presentation;
            $titles[] = $value->abstract_title;
        }
        return ['user' => $user, 'roles' => $roles, 'titles' => $titles];
    }

    public function getInvoices()
    {
        return InvoiceNotif::all()->pluck('invoice_number', 'invoice_id');
    }

    public function getPaymentInvoice($paymentId)
    {
        return PaymentNotif::with('invoice')->find($paymentId);
    }

    public function store(StorePaymentNotifRequest $request)
    {
        $data = $request->validated();
        $data['nominal'] = str_replace('.','',$data['nominal']);
        // $data['role'] = json_encode([$data['role']]);
        // $data['abstract_title'] = json_encode([$data['abstract_title']]);
        PaymentNotif::updateOrCreate(['paymentnotif_id' => $data['paymentnotif_id']], $data);
        // $invoice = InvoiceNotif::find('invoice_id');
        // $this->sendEmail($invoice->user_id);
    }

    private function sendEmail($idUser)
    {
        $user = User::with(['userDetails'])->find($idUser);
        Mail::to($user->email)->send(new InvoiceNotificationMail($user->userDetails));
    }

    public function generateTemplate($invoiceId)
    {
        $invoice = InvoiceNotif::find($invoiceId);
        $attribut = $invoice->atribut;
        $template = new TemplateProcessor(resource_path('template/invoice_template.docx'));
        $template->setValue('{{invoice_number}}', $invoice->invoice_number);
        // $template->setValue('{{invoice_number}}', $invoice->invoice_number);
        // $template->setValue('{{invoice_number}}', $invoice->invoice_number);
        // $template->setValue('{{invoice_number}}', $invoice->invoice_number);
        // $template->setValue('{{invoice_number}}', $invoice->invoice_number);

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        Settings::setPdfRendererPath($domPdfPath);
        Settings::setPdfRendererName('DomPDF');
        $path = public_path('invoice-'.microtime(true));
        $pathDocx = $path.'.docx';
        $pathPdf = $path.'.pdf';
        $template->saveAs($pathDocx);

        $phpWord = IOFactory::load($pathDocx);
        $xmlWriter = IOFactory::createWriter($phpWord,'PDF');
        $xmlWriter->save($pathPdf);
        unlink($pathDocx);
        return $pathPdf;
    }
}
