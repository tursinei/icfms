<?php

namespace App\Services;

use App\Http\Requests\StorefullPaperRequest;
use App\Models\FullPaper;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\Common\Creator\Style\BorderBuilder;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
use Yajra\DataTables\Facades\DataTables;

class FullpaperService
{

    public function listTable($iduser)
    {
        $data = FullPaper::join('abstract_file AS a', 'a.abstract_id', 'full_paper.abstract_id')
            ->join('m_topic AS t', 't.topic_id', 'a.topic_id')->where('full_paper.user_id', $iduser)
            ->orderBy('full_paper.created_at', 'DESC')
            ->get(['paper_id', 't.name as topic', 'full_paper.created_at','presenter','presentation', 'authors', 'a.paper_title as title']);
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<a class="btn btn-success btn-xs" href="'.route('fullpaper.show',['fullpaper' => $row->paper_id]).'"
                title="Download Paper File" target="_blank" ><i class="fa fa-download"></i></a>&nbsp;
                <button data-href="' . route('fullpaper.destroy', ['fullpaper' => $row->paper_id]) . '"
                data-id="' . $row->paper_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Paper "><i class="fa fa-trash-o"></i></button>';
        })->addColumn('date_upload', function ($row) {
            return $row->created_at->format('d-m-Y');
        })->rawColumns(['action', 'date_upload'])->make(true);
    }

    private function getFullpaper()
    {
        return FullPaper::join('abstract_file AS a', 'a.abstract_id', 'full_paper.abstract_id')
            ->join('m_topic AS t', 't.topic_id', 'a.topic_id')
            ->join('users AS u', 'u.id', 'full_paper.user_id')
            ->join('users_details AS ud', 'u.id', 'ud.user_id')
            ->orderBy('full_paper.created_at', 'DESC')
            ->get(['paper_id','u.email','ud.title as prefix','ud.affiliation','ud.country',
                    'u.name as fullname' ,'t.name as topic','full_paper.created_at',
                    'presenter','a.presentation', 'authors', 'a.paper_title as title' ]);
    }
    //full paper tanpa user
    public function listFullpaper()
    {
        $data = $this->getFullpaper();
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<a class="btn btn-success btn-xs" href="'.route('fullpaper.show',['fullpaper' => $row->paper_id]).'"
                title="Download Paper File" target="_blank" ><i class="fa fa-download"></i></a>&nbsp;
                <button data-href="' . route('fullpaper.destroy', ['fullpaper' => $row->paper_id]) . '"
                data-id="' . $row->paper_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Paper "><i class="fa fa-trash-o"></i></button>';
        })->addColumn('date_upload', function ($row) {
            return $row->created_at->format('d-m-Y');
        })->rawColumns(['action', 'date_upload'])->make(true);
    }

    public function simpan(StorefullPaperRequest $request)
    {
        $data = $request->validated();
        unset($data['paper_file']);
        $file = $request->file('paper_file');
        $dirUpload = 'dokumen_fullpaper';
        $nameFileInServer = 'paper-'.$data['user_id'].'-'.date("ymdHis").'.'.$file->getClientOriginalExtension();
        $titleFile = substr($request->input('title'),0,125);
        $title = str_replace(' ','_', $titleFile);
        $data['file_name'] = urlencode($title).'.'.$file->getClientOriginalExtension();
        $data['file_path'] = $dirUpload.'/'.$nameFileInServer;
        $data['size'] = $file->getSize();
        $data['extensi'] = $file->getClientOriginalExtension();
        $file->move($dirUpload,$nameFileInServer);
        return FullPaper::create($data);
    }

    public function fullPaperInExcel()
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
        $writer->setColumnWidth(20, 1); //date
        $writer->setColumnWidth(40, 2); // email
        $writer->setColumnWidth(20, 3); // title
        $writer->setColumnWidth(40, 4); // name
        $writer->setColumnWidth(35, 5); // Affiliation
        $writer->setColumnWidth(20, 6); // Country
        $writer->setColumnWidth(37, 7); // Presentation
        $writer->setColumnWidth(37, 8); // Presentter Name
        $writer->setColumnWidth(20, 9); // Topic
        $writer->setColumnWidth(20, 10); // Authors
        $writer->setColumnWidth(100, 11); // Paper title

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
        $namaKolom = ['Date','Email','Title' ,'Name', 'Affiliation','Country',
                        'Presentation','Presenter Name','Topic', 'Authors', 'Paper Title'];
        $header = WriterEntityFactory::createRowFromArray($namaKolom, $styleHeader);
        $writer->addRow($header);

        $fullpaperData = $this->getFullpaper();
        $styleCenter = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::CENTER)->build();
        $styleLeft = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::LEFT)->build();

        foreach ($fullpaperData as $row) {
            $tgl = $row->created_at->format('d-m-Y');
            $baris = [
                    WriterEntityFactory::createCell($tgl, $styleCenter),
                    WriterEntityFactory::createCell($row->email, $styleLeft),
                    WriterEntityFactory::createCell($row->prefix, $styleCenter),
                    WriterEntityFactory::createCell($row->fullname, $styleLeft),
                    WriterEntityFactory::createCell($row->affiliation, $styleLeft),
                    WriterEntityFactory::createCell($row->country, $styleLeft),
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
