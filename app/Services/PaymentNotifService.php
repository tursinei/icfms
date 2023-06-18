<?php

namespace App\Services;

use App\Mail\InvoiceNotificationMail;
use App\Mail\ReceiptNotificationMail;
use App\Models\AbstractFile;
use App\Models\Invoice;
use App\Models\InvoiceNotif;
use App\Models\PaymentNotif;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;

class PaymentNotifService
{
    private function getInvoiceUser(){
        $isMember = (Session::get('icfms_tipe_login') == IS_MEMBER);
        $iduser = Auth::user()->id;
        return Invoice::join('users AS u', 'u.id', 'user_id')
        ->whereNotNull('payment_tgl')
        ->when($isMember, function ($query) use ($iduser) {
            $query->where('user_id', $iduser);
        })->orderBy('created_at', 'DESC')
            ->get(['invoice.*', 'u.email', 'u.name']);
    }

    public function listPayment(){
        $data = $this->getInvoiceUser();
        return DataTables::of($data)->addColumn('actions', function ($row) {
            $delBtn = '';
            if(Session::get('icfms_tipe_login' == IS_ADMIN)){
                $delBtn = '<button data-href="' . route('payment-notification.destroy', ['payment_notification' => $row->invoice_id]) . '"
                data-id="' . $row->invoice_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Paper "><i class="fa fa-trash-o"></i></button>';
            }
            return '<a class="btn btn-success btn-xs" href="' . route('payment.file', ['invoiceId' => $row->invoice_id]) . '"
                title="Download Payment Receipt" target="_blank" ><i class="fa fa-download"></i></a>&nbsp;'.$delBtn;
        })->addColumn('title', function ($row) {
            return  $row->attribut['title'];
        })->addColumn('fullname', function ($row) {
            return  $row->attribut['fullname'];
        })->addColumn('affiliation', function ($row) {
            return  $row->attribut['affiliation'];
        })->addColumn('country', function ($row) {
            return  $row->attribut['country'];
        })->addColumn('abstract', function ($row) {
            return implode(', ', json_decode($row->abstract_title, true));
        })->addColumn('role', function ($row) {
            return implode(', ', json_decode($row->role, true));
        })->addColumn('prefnominal', function ($row) {
            return  $row->currency . ' ' . $row->nominal;
        })->rawColumns(['actions', 'title', 'fullname', 'affiliation',
                        'country', 'prefnominal','abstract', 'role'])->make(true);
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
        return Invoice::all()->pluck('invoice_number', 'invoice_id');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['nominal'] = str_replace('.','',$data['nominal']);
        return Invoice::where(['invoice_id' => $data['invoice_id']])->update(['payment_tgl' => $data['payment_tgl']]);
    }

    public function delete($invoice)
    {
        $invoice = Invoice::find($invoice);
        $invoice->payment_tgl = null;
        return $invoice->save();
    }
    private function sendEmail($idUser, $invoiceId)
    {
        $path = $this->generateTemplate($invoiceId);
        $user = User::with(['userDetails'])->find($idUser);
        Mail::to($user->email)->send(new ReceiptNotificationMail($user->userDetails, $invoiceId));
        unlink($path);
    }

    public function generateTemplate($invoiceId, $download = false)
    {
        $invoice = Invoice::find($invoiceId);
        $attribut = $invoice->attribut;

        $cur = $invoice->currency == 'USD' ? '$' : 'Rp';
        $data = [
            'invoice_number'   => $invoice->invoice_number,
            'title'            => $attribut['title'],
            'role'             => implode(',', json_decode($invoice->role)),
            'country'          => $attribut['country'],
            'nominal'          => $cur . ' ' . number_format($invoice->nominal),
            'fullname'         => $attribut['fullname'],
            'affiliation'      => $attribut['affiliation'],
            'date'             => date('d F Y', strtotime($invoice->payment_tgl)),
            'abstract_title'   => implode('<br/>', json_decode($invoice->abstract_title)),
        ];

        // return view('template.receipt_template', $data);
        $path = public_path('payment-' . microtime(true) . '.pdf');
        $pdf = Pdf::loadView('template.receipt_template', $data);
        if($download){
            return $pdf->download('payment_receipt-'.str_replace(' ','',$attribut['fullname']).'.pdf');
        }
        $pdf->save($path);
        return $path;
    }

    public function excelFile()
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $defaultStyle = (new StyleBuilder)->setFontName('Arial')->setFontSize(11)->build();
        $writer->setDefaultRowStyle($defaultStyle);

        $styleHeader = new Style();
        $styleHeader->setFontBold();
        $styleHeader->setFontName('Arial Narrow');
        $styleHeader->setShouldWrapText(false);
        $styleHeader->setFontSize(12);

        $getInvoices = $this->getInvoiceUser();

        $writer->openToBrowser('list-receipt.xlsx');

        $writer->setColumnWidth(10, 1); // Date
        $writer->setColumnWidth(40, 2); // Invoice
        $writer->setColumnWidth(25, 3); // Email
        $writer->setColumnWidth(10, 4); // Title
        $writer->setColumnWidth(40, 5); // Fullname
        $writer->setColumnWidth(25, 6); // Affiliation
        $writer->setColumnWidth(20, 7); // Country
        $writer->setColumnWidth(20, 8); // Nominal
        $writer->setColumnWidth(20, 9); // Payment Date

        $title1 = WriterEntityFactory::createCell('List Payments Receipt', $styleHeader);
        $singleRow = WriterEntityFactory::createRow([$title1]);
        $writer->addRow($singleRow);

        $writer->addRow(WriterEntityFactory::createRow());
        $border = (new BorderBuilder)->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)->build();
        $styleHeader->setBorder($border);
        $styleHeader->setCellAlignment(CellAlignment::CENTER);
        $styleHeader->setBackgroundColor(Color::rgb(218, 227, 243));

        $namaKolom = [
            'Date', 'Invoice', 'Email', 'Title', 'Fullname', 'Affiliation', 'Country', 'Nominal', 'Payment Date'
        ];
        $header = WriterEntityFactory::createRowFromArray($namaKolom, $styleHeader);
        $writer->addRow($header);
        $styleCenter = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::CENTER)->build();
        $styleLeft = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::LEFT)->build();

        foreach ($getInvoices as $row) {
            $date = date('d/m/Y', strtotime($row->tgl_invoice));
            $tglPayment = date('d F Y', strtotime($row->payment_tgl));
            $perBaris = [
                WriterEntityFactory::createCell($date, $styleCenter),
                WriterEntityFactory::createCell($row->invoice_number, $styleLeft),
                WriterEntityFactory::createCell($row->email, $styleLeft),
                WriterEntityFactory::createCell($row->attribut['title'], $styleCenter),
                WriterEntityFactory::createCell($row->attribut['fullname'], $styleLeft),
                WriterEntityFactory::createCell($row->attribut['affiliation'], $styleCenter),
                WriterEntityFactory::createCell($row->attribut['country'], $styleCenter),
                WriterEntityFactory::createCell($row->currency . ' ' . $row->nominal, $styleLeft),
                WriterEntityFactory::createCell($tglPayment, $styleCenter),
            ];
            $writer->addRow(WriterEntityFactory::createRow($perBaris));
        }
        $writer->close();
    }

    public function storePayment(Request $request)
    {
        $data = $request->all();
        $invoice = Invoice::where('order_id', $data['order_id'])->first();
        $invoice->status = ($data['status_code'] == 201 ? 2 : ($data['status_code'] == 200 ? 3 : 4));
        $invoice->keterangan = $data['status_message'];
        $invoice->payment_method = $data['payment_type'];
        $invoice->feedback = json_encode($data);
        return $invoice->save();
    }
}
