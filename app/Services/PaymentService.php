<?php

namespace App\Services;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Yajra\DataTables\Facades\DataTables;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

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
        return Payments::updateOrCreate(['payment_id' => $data['payment_id']],$data);
    }

    private function getPayments()
    {
        return Payments::join('users', 'users.id', 'payments.user_id')
        ->join('users_details', 'users.id', 'users_details.user_id')
        ->orderBy('payments.created_at', 'DESC')
        ->get(['payment_id', 'users.name', 'payments.created_at', 'users_details.affiliation','currency', 'nominal']);
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
        $writer->setColumnWidth(20, 1);
        $writer->setColumnWidth(40, 2);
        $writer->setColumnWidth(30, 3);
        $writer->setColumnWidth(10, 4);
        $writer->setColumnWidth(30, 5);

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
        $namaKolom = ['Date', 'Name', 'Affiliation','Currency','Nominal'];
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
                    WriterEntityFactory::createCell($row->name, $styleLeft),
                    WriterEntityFactory::createCell($row->affiliation, $styleLeft),
                    WriterEntityFactory::createCell($row->currency, $styleCenter),
                    WriterEntityFactory::createCell($uang, $styleLeft)];
            $writer->addRow(WriterEntityFactory::createRow($baris));
        }
        $writer->close();
    }
}
