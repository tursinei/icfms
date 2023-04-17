<?php

namespace App\Services;

use App\Http\Requests\StoreinvoiceRequest;
use App\Mail\InvoiceNotificationMail;
use App\Models\AbstractFile;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Midtrans\Config;
use Midtrans\Snap;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;

class InvoiceService
{
    public function listInvoice(){
        $data = Invoice::join('users AS u','u.id', 'user_id')
            ->orderBy('created_at', 'DESC')
            ->get(['invoice.*','u.email','u.name']);
        return DataTables::of($data)->addColumn('actions', function($row){
            return '<a class="btn btn-success btn-xs" href="' . route('invoice.file',['invoiceId' => $row->invoice_id]) . '"
                title="Download Invoice" target="_blank" ><i class="fa fa-download"></i></a>&nbsp;
                <button data-href="' . route('invoice-notification.destroy', ['invoice_notification' => $row->invoice_id]) . '"
                data-id="' . $row->invoice_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Paper "><i class="fa fa-trash-o"></i></button>';
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

    public function getInvoiceByUser($idUser)
    {
        return Invoice::with(['user','userDetail'])->where('user_id',$idUser)->first();
    }

    public function getSnapData($idUser)
    {
        $data = $this->getInvoiceByUser($idUser);
        Config::$serverKey = 'SB-Mid-server-6y8YQlyCuGBRRYpNZIhoOMJB';
        Config::$clientKey = 'SB-Mid-client-9-i_cwbk3EUIzNdc';

        $transaction_details = [
            'order_id' => $data->invoice_id . '-' . rand(),
            'gross_amount' => $data->nominal
        ];
        $transaction = [
            'enabled_payments' => ['credit_card', 'bank_transfer'],
        ];
        $fee = 5000;
        if ($data->currency == 'USD') {
            $transaction['enabled_payments'] = ['credit_card'];
            $fee = ($data->nominal * 2.9 / 100) + 2000; // fee untuk credit cards
        }
        $data->fee = $fee;
        $transaction_details['gross_amount'] = $data->nominal + $fee;
        $transaction['transaction_details'] = $transaction_details;
        $data->snap_token = '';
        try {
            $data->snap_token = Snap::getSnapToken($transaction);
        } catch (\Throwable $th) {
            abort(501, $th->getMessage());
        }
        $data->total = $transaction_details['gross_amount'];
        return $data;
    }

    public function store(StoreinvoiceRequest $request)
    {
        $data = $request->validated();
        $data['nominal'] = str_replace('.','',$data['nominal']);
        $data['role'] = json_encode([$data['role']]);
        $data['abstract_title'] = json_encode([$data['abstract_title']]);
        Invoice::updateOrCreate(['invoice_id' => $data['invoice_id']], $data);
        $this->sendEmail($data['user_id']);
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
