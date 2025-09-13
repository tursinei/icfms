<?php

namespace App\Services;

use App\Mail\InvoiceNotificationMail;
use App\Mail\ReceiptNotificationMail;
use App\Models\AbstractFile;
use App\Models\Invoice;
use App\Models\InvoiceNotif;
use App\Models\LogNotifPayment;
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
use Yajra\DataTables\Facades\DataTables;

class PaymentNotifService
{
    private function getInvoiceUser()
    {
        $isMember = (Session::get('icfms_tipe_login') == config('app.roles.member'));
        $iduser = Auth::user()->id;
        return Invoice::join('users AS u', 'u.id', 'user_id')
            ->whereNotNull('payment_tgl')
            ->when($isMember, function ($query) use ($iduser) {
                $query->where('user_id', $iduser)->where('is_payment_confirm', true);
            })->orderBy('created_at', 'DESC')
            ->get(['invoice.*', 'u.email', 'u.name']);
    }

    public function listPayment()
    {
        $data = $this->getInvoiceUser();
        return DataTables::of($data)->addColumn('actions', function ($row) {
            $delBtn = '';
            if (Session::get('icfms_tipe_login') == config('app.roles.admin')) {
                $delBtn = '<button data-href="' . route('payment-notification.destroy', ['payment_notification' => $row->invoice_id]) . '"
                data-id="' . $row->invoice_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Paper "><i class="fa fa-trash-o"></i></button>';
            }
            return '<a class="btn btn-success btn-xs" href="' . route('payment.file', ['invoiceId' => $row->invoice_id]) . '"
                title="Download Payment Receipt" target="_blank" ><i class="fa fa-download"></i></a>&nbsp;' . $delBtn;
        })->addColumn('detail', function ($row) {
            return  $row->attribut['title'] . ' ' . $row->attribut['fullname'] . '<br/>'
                . $row->attribut['affiliation'] . '<br/>' . $row->attribut['country'];
        })->addColumn('konfirmasi', function ($row) {
            $cheked = $row->is_payment_confirm ? 'checked="checked"' : '';
            return '<input ' . $cheked . ' class="cek-konfirm" type="checkbox" value="' . $row->invoice_id . '" />';
        })->addColumn('title', function ($row) {
            return  $row->attribut['title'];
        })->addColumn('fullname', function ($row) {
            return  $row->attribut['fullname'];
        })->addColumn('affiliation', function ($row) {
            return  $row->attribut['affiliation'];
        })->addColumn('country', function ($row) {
            return  $row->attribut['country'];
        })->addColumn('tgl_payment_orderid', function ($row) {
            return $row->payment_tgl->format('d-m-Y') . '<br/><div class="bg-primary"><strong>' . $row->order_id . '</strong></div>';
        })->addColumn('abstract', function ($row) {
            return $row->abstract_title;
        })->addColumn('role', function ($row) {
            return $row->role;
        })->addColumn('prefnominal', function ($row) {
            return  $row->currency . ' ' . $row->nominal;
        })->rawColumns(['actions', 'detail', 'prefnominal', 'konfirmasi', 'abstract', 'role', 'tgl_payment_orderid'])->make(true);
    }

    public function userById($id)
    {
        $user = User::join('users_details as ud', 'users.id', 'ud.user_id')->where('id', $id)
            ->get(['title', 'name', 'affiliation', 'country'])->first();
        $abstract = AbstractFile::where('user_id', $user->user_id)->get(['presentation', 'abstract_title']);
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
        $data['nominal'] = str_replace('.', '', $data['nominal']);
        $invoice = Invoice::find($data['invoice_id']);
        $invoice->payment_tgl = $data['payment_tgl'];
        $invoice->is_payment_confirm = true;
        $invoice->status   = 2; //success
        $invoice->keterangan = "Konfirmasi manual";
        $this->sendEmail($invoice->user_id, $invoice->invoice_id);
        return $invoice->save();
    }

    function storeKonfirmasi(Request $request)
    {
        $data = $request->all();
        $invoice = Invoice::find($data['invoice_id']);
        $invoice->is_payment_confirm = $data['is_confirm'];
        $invoice->status = $data['is_confirm'] ? 3 : 2;
        if ($data['is_confirm']) {
            $this->sendEmail($invoice->user_id, $invoice->invoice_id);
        }
        return $invoice->save();
    }

    public function delete($invoice)
    {
        $invoice = Invoice::find($invoice);
        $invoice->payment_tgl = null;
        return $invoice->save();
    }

    public function sendEmail($idUser, $invoiceId, $debug = false)
    {
        $invoice = $this->generateTemplate($invoiceId);
        $user = User::with(['userDetails'])->find($idUser);
        if ($debug) {
            return (new ReceiptNotificationMail($user, $invoice))->render();
        }
        Mail::to($user->email)->send(new ReceiptNotificationMail($user, $invoice));
        unlink($invoice->path);
    }

    public function generateTemplate($invoiceId, $download = false)
    {
        $invoice = Invoice::find($invoiceId);
        $attribut = $invoice->attribut;

        $cur = $invoice->currency == 'USD' ? '$' : 'Rp';
        $data = [
            'invoice_number'   => $invoice->invoice_number,
            'title'            => $attribut['title'],
            'role'             => $invoice->role,
            'country'          => $attribut['country'],
            'nominal'          => $cur . ' ' . number_format($invoice->nominal),
            'fullname'         => $attribut['fullname'],
            'affiliation'      => $attribut['affiliation'],
            'date'             => date('d F Y', strtotime($invoice->payment_tgl)),
            'abstract_title'   => $invoice->abstract_title,
            'attribute'        => $attribut,
            'currency'         => $invoice->currency,
        ];
        $view = $invoice->jenis == 'hotel' ? 'template.receipt-hotel' : 'template.receipt-registration';
        // return view($view, $data);
        $path = public_path('payment-' . microtime(true) . '.pdf');
        $pdf = Pdf::loadView($view, $data);
        if ($download) {
            return $pdf->download('payment_receipt-' . str_replace(' ', '', $attribut['fullname']) . '.pdf');
        }
        $pdf->save($path);
        $invoice->path = $path;
        return $invoice;
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
        $invoice->status = ($data['status_code'] == 201 ? 1 : ($data['status_code'] == 200 ? 2 : 4));
        $invoice->keterangan = $data['status_message'] ?? 'Transaksi Sedang diproses (' . date('Hi') . ')';
        $invoice->payment_method = $data['payment_type'] ?? "";
        $invoice->payment_tgl = date('Y-m-d');
        $invoice->feedback = json_encode($data);
        return $invoice->save();
    }

    public function handleNotif(Request $request)
    {
        LogNotifPayment::create([
            'order_id' => $request->input('order_id', '-'),
            'url_feedback'  => $request->url(),
            'respon_body'   => json_encode($request->all()),
            'status_code'   => json_encode($request->input('status_code', '000')),
            'status_message'   => json_encode($request->input('status_message', '-no message-'))
        ]);
        if (!$request->has('status_code')) {
            $request->merge(['status_code' => 203]);
            $request->merge(['status_message' => ($data['status_message'] ?? 'Tidak ada status message')]);
        }
        return $this->storePayment($request);
    }
}
