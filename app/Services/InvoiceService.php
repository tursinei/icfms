<?php

namespace App\Services;

use App\Http\Requests\StoreinvoiceRequest;
use App\Mail\InvoiceNotificationMail;
use App\Models\AbstractFile;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UserDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Midtrans\Snap;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Yajra\DataTables\Facades\DataTables;

class InvoiceService
{
    const status = ['Invoice Created','Waiting Payment','Waiting Payment Confirmation', 'Paid Successfully','Payment Failed'];
    const statusLabel = ['label-primary','label-info','label-warning','label-success','label-danger'];

    private function getInvoice()
    {
        $isMember = (Session::get('icfms_tipe_login') == IS_MEMBER);
        $iduser = Auth::user()->id;
        return Invoice::join('users AS u', 'u.id', 'user_id')
            ->orderBy('created_at', 'DESC')->select(['invoice.*', 'u.email', 'u.name'])
            ->when($isMember, function ($query) use ($iduser) {
                $query->where("user_id", $iduser);
            })->get();
    }

    public function listInvoiceUser()
    {
        $data = $this->getInvoice();
        return DataTables::of($data)->addColumn('actions',function ($row) {
            return '<button class="btn btn-success btn-xs btn-payment" data-id="'.$row->invoice_id. '"
                title="Credit Card Payment" ><i class="fa fa-arrow-right"></i></button>';
        })->addColumn('description', function($row){
            return 'Registration Fee as '.$row->role.'<br/>Paper title : '. $row->abstract_title;
        })->addColumn('status', function ($row) {
            $return = self::status[$row->status];
            $class  = self::statusLabel[$row->status];
            return '<span class="label '.$class.'">'.$return.'</span>';
        })->addColumn('terbilang', function ($row) {
            return  $row->currency . ' ' . $row->nominal;
        })->rawColumns(['actions','description','terbilang', 'status'])->make(true);
    }

    public function listInvoice()
    {
        $data = $this->getInvoice();
        return DataTables::of($data)->addColumn('actions', function ($row) {
            $delBtn = '';
            if (Session::get('icfms_tipe_login') == IS_ADMIN) {
                $delBtn = ' <button data-href="' . route('invoice-notification.destroy', ['invoice_notification' => $row->invoice_id]) . '"
                data-id="' . $row->invoice_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Paper "><i class="fa fa-trash-o"></i></button>';
            }
            return '<a class="btn btn-success btn-xs" href="' . route('invoice.file', ['invoiceId' => $row->invoice_id]) . '"
                title="Download Invoice" target="_blank" ><i class="fa fa-download"></i></a>&nbsp;' . $delBtn;
        })->addColumn('title', function ($row) {
            return  $row->attribut['title'];
        })->addColumn('fullname', function ($row) {
            return  $row->attribut['fullname'];
        })->addColumn('affiliation', function ($row) {
            return  $row->attribut['affiliation'];
        })->addColumn('country', function ($row) {
            return  $row->attribut['country'];
        })->addColumn('prefnominal', function ($row) {
            return  $row->currency . ' ' . number_format($row->nominal);
        })->addColumn('abstract', function ($row) {
            return $row->abstract_title;
        })->addColumn('role', function ($row) {
            return ucfirst($row->role);
        })->rawColumns([
            'actions', 'title', 'fullname',
            'affiliation', 'country', 'prefnominal', 'abstract', 'role'
        ])->make(true);
    }

    public function userById($id)
    {
        $user = User::join('users_details as ud', 'users.id', 'ud.user_id')->where('id', $id)
            ->get(['title', 'name', 'affiliation', 'country','user_id','presentation as role'])->first();
        $abstract = AbstractFile::where('user_id', $user->user_id)->get(['presentation', 'abstract_title']);
        $titles = [];
        foreach ($abstract as $value) {
            $titles[] = $value->abstract_title;
        }
        return ['user' => $user, 'titles' => $titles];
    }

    public function getInvoiceByUser($idUser)
    {
        return Invoice::with(['user', 'userDetail'])->where('user_id', $idUser)->first();
    }

    public function getInvoiceById($id)
    {
        return Invoice::with(['user', 'userDetail'])->where('invoice_id', $id)->first();
    }

    public function getSnapData($idInvoice)
    {
        $dataInvoice = $this->getInvoiceById($idInvoice);
        Config::$clientKey = config('midtrans.client_key');
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $transaction_details = [
            'order_id' => $dataInvoice->invoice_id. '-' . rand(),
            'gross_amount' => $dataInvoice->nominal
        ];
        $transaction = [
            // 'enabled_payments' => ['bank_transfer'],
        ];
        $fee = 5000; // fee untuk bank transfer
        $nominal = $dataInvoice->nominal;
        $total = $nominal + $fee;
        if ($dataInvoice->currency == 'USD') {
          //  $transaction['enabled_payments'] = ['credit_card'];
            $konversi = self::konversiDollar($dataInvoice->nominal);
            $nominal = $konversi['converted'];
            if($nominal == 0){
                abort(501, 'Dollar Convertion was failed');
            }
            $fee = round($nominal * 2.5 / 100,0) + 2000; // fee untuk credit cards
            $total = $nominal + $fee;
        }
        $transaction_details['gross_amount'] = $total;
        $transaction['transaction_details'] = $transaction_details;
        $userDetail = UserDetail::with('user')->find($dataInvoice->user_id);
        $transaction['customer_details'] = [
            'first_name' => ucfirst($userDetail->firstname),
            'last_name' => ucfirst($userDetail->lastname),
            'email' => $userDetail->user->email,
            'phone' => $userDetail->mobilenumber,
        ];
        try {
            if($dataInvoice->status == 0){
                $dataInvoice->snap_token = Snap::getSnapToken($transaction);
                $dataInvoice->nominal_rupiah = $total;
                $dataInvoice->payment_fee = $fee;
                $dataInvoice->order_id = $transaction_details['order_id'];
                $dataInvoice->save();
            }
        } catch (\Throwable $th) {
            abort(501, $th->getMessage());
        }
        if($dataInvoice->currency == 'USD'){
            $dataInvoice->inRupiah = $nominal; // tidak ada kolom inRupiah di table invoice
        }
        $dataInvoice->fee = $fee;
        $dataInvoice->total = $transaction_details['gross_amount'];
        $dataInvoice->urlSnapJs = Config::$isProduction ? 'https://app.midtrans.com/snap/snap.js' :
        'https://app.sandbox.midtrans.com/snap/snap.js';
        return $dataInvoice;
    }

    public function store(StoreinvoiceRequest $request)
    {
        $data = $request->validated();
        $data['nominal'] = str_replace('.', '', $data['nominal']);
        $data['role'] = $data['role'];
        $data['abstract_title'] = $data['abstract_title'];
        $invoice = Invoice::updateOrCreate(['invoice_id' => $data['invoice_id']], $data);
        $this->sendEmail($data['user_id'], $invoice->invoice_id);
    }

    private function sendEmail($idUser, $invoiceId)
    {
        $path = $this->generateTemplate($invoiceId);
        $user = User::with(['userDetails'])->find($idUser);
        Mail::to($user->email)->send(new InvoiceNotificationMail($user->userDetails, $path));
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
            'role'             => $invoice->role,
            'country'          => $attribut['country'],
            'nominal'          => $cur . ' ' . number_format($invoice->nominal),
            'fullname'         => $attribut['fullname'],
            'affiliation'      => $attribut['affiliation'],
            'abstract_title'   => $invoice->abstract_title,
        ];

        // return view('template.invoice_template', $data);
        $path = public_path('invoice-' . microtime(true) . '.pdf');
        $pdf = Pdf::loadView('template.invoice_template', $data);
        if ($download) {
            return $pdf->download('invoice-' . str_replace(' ', '', $attribut['fullname'] . '.pdf'));
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

        $getInvoices = $this->getInvoice();

        $writer->openToBrowser('list-invoice.xlsx');

        $writer->setColumnWidth(10, 1); // Date
        $writer->setColumnWidth(40, 2); // Invoice
        $writer->setColumnWidth(25, 3); // Email
        $writer->setColumnWidth(10, 4); // Title
        $writer->setColumnWidth(40, 5); // Fullname
        $writer->setColumnWidth(25, 6); // Affiliation
        $writer->setColumnWidth(20, 7); // Country
        $writer->setColumnWidth(20, 8); // Nominal

        $title1 = WriterEntityFactory::createCell('List Payments Invoice', $styleHeader);
        $singleRow = WriterEntityFactory::createRow([$title1]);
        $writer->addRow($singleRow);

        $writer->addRow(WriterEntityFactory::createRow());
        $border = (new BorderBuilder)->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)->build();
        $styleHeader->setBorder($border);
        $styleHeader->setCellAlignment(CellAlignment::CENTER);
        $styleHeader->setBorder($border);
        $styleHeader->setBackgroundColor(Color::rgb(218, 227, 243));

        $namaKolom = [
            'Date', 'Invoice', 'Email', 'Title', 'Fullname', 'Affiliation', 'Country', 'Nominal'
        ];
        $header = WriterEntityFactory::createRowFromArray($namaKolom, $styleHeader);
        $writer->addRow($header);
        $styleCenter = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::CENTER)->build();
        $styleLeft = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::LEFT)->build();

        foreach ($getInvoices as $row) {
            $date = date('d/m/Y', strtotime($row->tgl_invoice));
            $perBaris = [
                WriterEntityFactory::createCell($date, $styleCenter),
                WriterEntityFactory::createCell($row->invoice_number, $styleLeft),
                WriterEntityFactory::createCell($row->email, $styleLeft),
                WriterEntityFactory::createCell($row->attribut['title'], $styleCenter),
                WriterEntityFactory::createCell($row->attribut['fullname'], $styleLeft),
                WriterEntityFactory::createCell($row->attribut['affiliation'], $styleCenter),
                WriterEntityFactory::createCell($row->attribut['country'], $styleCenter),
                WriterEntityFactory::createCell($row->currency . ' ' . $row->nominal, $styleLeft),
            ];
            $writer->addRow(WriterEntityFactory::createRow($perBaris));
        }
        $writer->close();
    }
    /**
     * return array [converted, rate]
     */
    public static function konversiDollar($priceInUsd)
    {
        try {
            $ch = curl_init();
            if($ch == false){
                abort(501, 'Curl init failed');
            }
            curl_setopt($ch, CURLOPT_URL, "https://v6.exchangerate-api.com/v6/21619cb7e5ecf68b90c8982d/latest/IDR");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            if (false !== $output) {
                try {
                    $response_curl = json_decode($output);
                    return [
                        'converted' => round($priceInUsd / $response_curl->conversion_rates->USD, 0),
                        'rate'      => $response_curl->conversion_rates->USD,
                    ];
                } catch (Exception $e) {
                    abort(501, $e->getMessage());
                }
            }else {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
        } catch (\Throwable $th) {
            abort(501, 'CURL throw ::  '.$th->getMessage());
        } finally{
            if(is_resource($ch)){
                curl_close($ch);
            }
        }
    }
}
