<?php

namespace App\Services;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payments;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;

class PaymentService
{

    public function simpan(StorePaymentRequest $request)
    {
        $data = $request->validated();
        unset($data['note']);
        if($request->hasFile('note')){
            $file = $request->file('note');
            $dirUpload = 'dokumen_payment';
            $nameFileInServer = 'nota-'.$data['user_id'].'-'.date("ymdHis").'.'.$file->getClientOriginalExtension();
            $title = 'note-payment-'.date('ymd').'.'.$file->getClientOriginalExtension();
            $data['file_name'] = $title;
            $data['file_path'] = $dirUpload.'/'.$nameFileInServer;
            $file->move($dirUpload,$nameFileInServer);
        }
        return Payments::updateOrCreate(['payment_id' => $data['payment_id']],$data);
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
        $writer->openToBrowser('list-fullpaper.xlsx');
        $writer->setColumnWidth(20, 1);
        $writer->setColumnWidth(40, 2);
        $writer->setColumnWidth(20, 3);
        $writer->setColumnWidth(30, 4);
        $writer->setColumnWidth(50, 5);
        $writer->setColumnWidth(50, 6);
        $writer->setColumnWidth(50, 7);

        $title1 = WriterEntityFactory::createCell('List FullPaper', $styleHeader);
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
        $namaKolom = ['Date', 'Name', 'Presentation','Presenter Name','Topic', 'Authors', 'Paper Title'];
        $header = WriterEntityFactory::createRowFromArray($namaKolom, $styleHeader);
        $writer->addRow($header);

        $fullpaperData = $this->getFullpaper();
        $styleCenter = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::CENTER)->build();
        $styleLeft = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::LEFT)->build();

        foreach ($fullpaperData as $row) {
            $tgl = $row->created_at->format('d-m-Y');
            $baris = [
                    WriterEntityFactory::createCell($tgl, $styleCenter),
                    WriterEntityFactory::createCell($row->fullname, $styleLeft),
                    WriterEntityFactory::createCell($row->presentation, $styleCenter),
                    WriterEntityFactory::createCell($row->presenter, $styleLeft),
                    WriterEntityFactory::createCell($row->topic, $styleLeft),
                    WriterEntityFactory::createCell($row->authors, $styleLeft),
                    WriterEntityFactory::createCell($row->title, $styleLeft)];
            $writer->addRow(WriterEntityFactory::createRow($baris));
        }
        $writer->close();
    }
}
