<?php

namespace App\Services;

use App\Http\Requests\StorePaymentRequest;
use App\Mail\PaymentNotificationMail;
use App\Models\Payments;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Yajra\DataTables\Facades\DataTables;


class PaymentService
{

    public function simpan(StorePaymentRequest $request)
    {
        $data = $request->validated();
        unset($data['note']);
        $data['nominal'] = str_replace('.','', $data['nominal']);
        if($request->hasFile('note')){
            $file = $request->file('note');
            $dirUpload = 'dokumen_payment';
            $nameFileInServer = 'nota-'.$data['user_id'].'.'.$file->getClientOriginalExtension();
            $title = 'note-payment-'.date('ymd').'.'.$file->getClientOriginalExtension();
            $data['file_name'] = $title;
            $data['file_path'] = $dirUpload.'/'.$nameFileInServer;
            $file->move($dirUpload,$nameFileInServer);
        }
        $isSend = $data['send_confirm'];
        unset($data['send_confirm']);
        if($isSend){
            $user = User::with(['userDetails'])->find($data['user_id']);
            Mail::to($user->email)->send(new PaymentNotificationMail($user->userDetails));
        }
        return Payments::updateOrCreate(['payment_id' => $data['payment_id']],$data);
    }

    private function getPayments()
    {
        return Payments::join('users', 'users.id', 'payments.user_id')
        ->join('users_details', 'users.id', 'users_details.user_id')
        ->leftJoin('invoice', 'invoice.invoice_id', 'payments.invoice_id')
        ->orderBy('payments.created_at', 'DESC')
        ->get(['payment_id','title', 'users.name', 'payments.created_at', 'users_details.affiliation',
            'users_details.presentation','payments.currency', 'payments.nominal','email', 'invoice_number']);
    }

    public function listTable()
    {
        $data = $this->getPayments();
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<a class="btn btn-info btn-xs" href="'.route('payment.show',['payment' => $row->payment_id, 'action' => 'view']).'"
                title="View Payment File" target="_blank"><i class="fa fa-file-o"></i></a>&nbsp;
                <a class="btn btn-success btn-xs" href="'.route('payment.show',['payment' => $row->payment_id, 'action' => 'download']).'"
                title="Download Payment File" target="_blank"><i class="fa fa-download"></i></a>&nbsp
                <button class="btn btn-danger btn-xs btn-delete" data-url="'.route('payment.destroy',['payment' => $row->payment_id]).'"
                title="Delete Payment"><i class="fa fa-trash"></i></button>';
        })->addColumn('presentation', function ($row) {
            return ucwords($row->presentation);
        })->addColumn('date_upload', function ($row) {
            return $row->created_at->format('d-m-Y');
        })->addColumn('terbilang', function($row){
            $mataUang = $row->currency == 'IDR' ? 'Rp' : '$';
            return $mataUang.' '.number_format($row->nominal,0,'.',',');
        })->rawColumns(['action', 'date_upload','terbilang'])->make(true);
    }

    public function paymentsInExcel()
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $defaultStyle = (new StyleBuilder)->setFontName('Arial')->setFontSize(11)->build();
        $writer->setDefaultRowStyle($defaultStyle);
        $styleHeader = new Style();
        $styleHeader->setFontBold();
        $styleHeader->setFontName('Arial Narrow');
        $styleHeader->setShouldWrapText(false);
        $styleHeader->setFontSize(12);
        $writer->openToBrowser('list-payments.xlsx');
        $writer->setColumnWidth(20, 1); // date
        $writer->setColumnWidth(30, 2); // email
        $writer->setColumnWidth(15, 3); // title
        $writer->setColumnWidth(40, 4); // name
        $writer->setColumnWidth(40, 5); // affiliation
        $writer->setColumnWidth(15, 6); // role
        $writer->setColumnWidth(15, 7); // currency
        $writer->setColumnWidth(25, 8); // nominal

        $title1 = WriterEntityFactory::createCell('List Payments', $styleHeader);
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
        $namaKolom = ['Date', 'Email','Title','Name', 'Affiliation','Permission Role','Currency','Nominal'];
        $header = WriterEntityFactory::createRowFromArray($namaKolom, $styleHeader);
        $writer->addRow($header);

        $fullpaperData = $this->getPayments();
        $styleCenter = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::CENTER)->build();
        $styleLeft = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::LEFT)->build();

        foreach ($fullpaperData as $row) {
            $tgl = $row->created_at->format('d-m-Y');
            $uang = number_format($row->nominal, 0, ',','.');
            $baris = [
                    WriterEntityFactory::createCell($tgl, $styleCenter),
                    WriterEntityFactory::createCell($row->email, $styleLeft),
                    WriterEntityFactory::createCell($row->title, $styleCenter),
                    WriterEntityFactory::createCell($row->name, $styleLeft),
                    WriterEntityFactory::createCell($row->affiliation, $styleLeft),
                    WriterEntityFactory::createCell($row->presentation, $styleLeft),
                    WriterEntityFactory::createCell($row->currency, $styleCenter),
                    WriterEntityFactory::createCell($uang, $styleLeft)];
            $writer->addRow(WriterEntityFactory::createRow($baris));
        }
        $writer->close();
    }
}
