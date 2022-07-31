<?php

namespace App\Services;

use App\Http\Requests\StoreAbstractFileRequest;
use App\Models\AbstractFile;
use Yajra\DataTables\Facades\DataTables;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Border;

use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
class AbstractService
{

    public function listTable($iduser)
    {
        $data = AbstractFile::join('m_topic', 'm_topic.topic_id', 'abstract_file.topic_id')->where('user_id', $iduser)
            ->orderBy('created_at', 'DESC')
            ->get(['abstract_id', 'name as topic', 'created_at', 'presentation', 'authors', 'abstract_title']);
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<a class="btn btn-success btn-xs" href="'.route('abstract.show',['abstract' => $row->abstract_id]).'"
                title="Download Abstract File" target="_blank"><i class="fa fa-download"></i></a>&nbsp;
                <button data-href="' . route('abstract.destroy', ['abstract' => $row->abstract_id]) . '"
                data-id="' . $row->abstract_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Abstract"><i class="fa fa-trash-o"></i></button>';
        })->addColumn('date_upload', function ($row) {
            return $row->created_at->format('d-m-Y');
        })->rawColumns(['action', 'date_upload'])->make(true);
    }

    private function getAbstract()
    {
        return AbstractFile::join('m_topic', 'm_topic.topic_id', 'abstract_file.topic_id')
        ->join('users', 'users.id', 'abstract_file.user_id')
        ->orderBy('created_at', 'DESC')
        ->get(['abstract_id','users.name as fullname' ,'m_topic.name as topic', 'abstract_file.created_at',
                'presentation', 'authors', 'abstract_title']);
    }

    public function listAbstracts()
    {
        $data = $this->getAbstract();
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<a class="btn btn-success btn-xs" href="'.route('abstract.show',['abstract' => $row->abstract_id]).'"
                title="Download Abstract File" target="_blank"><i class="fa fa-download"></i></a>&nbsp;
                <button data-href="' . route('abstract.destroy', ['abstract' => $row->abstract_id]) . '"
                data-id="' . $row->abstract_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Abstract"><i class="fa fa-trash-o"></i></button>';
        })->addColumn('date_upload', function ($row) {
            return $row->created_at->format('d-m-Y');
        })->rawColumns(['action', 'date_upload'])->make(true);
    }

    public function simpan(StoreAbstractFileRequest $request)
    {

        $data = $request->validated();
        unset($data['abstract_file']);
        $file = $request->file('abstract_file');
        $dirUpload = 'dokumen_abstract';
        $nameFileInServer = 'abstract-'.$data['user_id'].'-'.date("ymdHis").'.'.$file->getClientOriginalExtension();
        $titleFile = substr($data['abstract_title'],0,100);
        $title = str_replace(' ','_', $titleFile).'.'.$file->getClientOriginalExtension();
        $data['file_name'] = $title;
        $data['size'] = $file->getSize();
        $data['file_path'] = $dirUpload.'/'.$nameFileInServer;
        $data['extensi'] = $file->getClientOriginalExtension();

        $file->move($dirUpload,$nameFileInServer);
        return AbstractFile::create($data);
    }

    public function abstractExcel()
    {

        $writer = WriterEntityFactory::createXLSXWriter();
        $defaultStyle = (new StyleBuilder)->setFontName('Arial')->setFontSize(11)->build();
        $writer->setDefaultRowStyle($defaultStyle);
        $styleHeader = new Style();
        $styleHeader->setFontBold();
        $styleHeader->setFontName('Arial Narrow');
        $styleHeader->setShouldWrapText(false);
        $styleHeader->setFontSize(12);
        $writer->openToBrowser('list-abstracts.xlsx');
        $writer->setColumnWidth(30, 1);
        $writer->setColumnWidth(40, 2);
        $writer->setColumnWidth(25, 3);
        $writer->setColumnWidth(50, 4);
        $writer->setColumnWidth(45, 5);
        $writer->setColumnWidth(35, 6);

        $title1 = WriterEntityFactory::createCell('List Abstracts', $styleHeader);
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
        $namaKolom = ['Date', 'Name', 'Presentation', 'Topic', 'Authors', 'Abstract Title'];
        $header = WriterEntityFactory::createRowFromArray($namaKolom, $styleHeader);
        $writer->addRow($header);
        $abstractData = $this->getAbstract();
        $styleNumber = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::CENTER)->build();
        $styleLeft = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::LEFT)->build();

        foreach ($abstractData as $row) {
            $tgl = $row->created_at->format('d-m-Y');
            $baris = [
                    WriterEntityFactory::createCell($tgl, $styleNumber),
                    WriterEntityFactory::createCell($row->fullname, $styleLeft),
                    WriterEntityFactory::createCell($row->presentation, $styleNumber),
                    WriterEntityFactory::createCell($row->topic, $styleLeft),
                    WriterEntityFactory::createCell($row->authors, $styleLeft),
                    WriterEntityFactory::createCell($row->abstract_title, $styleLeft)];
            $writer->addRow(WriterEntityFactory::createRow($baris));
        }
        $writer->close();
    }
}
