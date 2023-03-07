<?php

namespace App\Services;

use App\Http\Requests\StoreAbstractFileRequest;
use App\Mail\AbstractNotificationMail;
use App\Models\AbstractFile;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
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
    const ROLES = ['oral', 'poster', 'audience', 'keynote speaker', 'Invited Speaker'];

    public function listTable($iduser)
    {
        $data = AbstractFile::join('m_topic', 'm_topic.topic_id', 'abstract_file.topic_id')->where('user_id', $iduser)
            ->orderBy('created_at', 'DESC')
            ->get(['abstract_id', 'name as topic', 'created_at', 'presentation', 'authors', 'abstract_title', 'is_presentation']);
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<a class="btn btn-success btn-xs" href="'.route('abstract.show',['abstract' => $row->abstract_id]).'"
                title="Download Abstract File" target="_blank"><i class="fa fa-download"></i></a>&nbsp;
                <button data-href="' . route('abstract.destroy', ['abstract' => $row->abstract_id]) . '"
                data-id="' . $row->abstract_id . '" class="btn btn-danger btn-xs btn-hapus"
                title="Delete Abstract"><i class="fa fa-trash-o"></i></button>';
        })->addColumn('date_upload', function ($row) {
            return $row->created_at->format('d-m-Y');
        })->addColumn('remarks',function($row){
            return $row->is_presentation ? 'Abstract with Presentation Only': 'Abstract with Full Paper Submission';
        })->rawColumns(['action', 'date_upload'])->make(true);
    }

    private function getAbstract()
    {
        return AbstractFile::join('m_topic', 'm_topic.topic_id', 'abstract_file.topic_id')
        ->join('users', 'users.id', 'abstract_file.user_id')
        ->join('users_details','users_details.user_id','users.id')
        ->orderBy('created_at', 'DESC')
        ->get(['abstract_id','is_presentation','users.name as fullname' ,'m_topic.name as topic', 'abstract_file.created_at',
                'abstract_file.presentation', 'authors', 'abstract_title', 'users_details.title','users.email', 'title','affiliation','country']);
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
        })->addColumn('remarks', function ($row) {
            return $row->is_presentation ? 'Abstract with Presentation Only' : 'Abstract with Full Paper Submission';
        })->rawColumns(['remarks','action', 'date_upload'])->make(true);
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
        $user = User::with(['userDetails'])->find($data['user_id']);
        $dataMail = [
            'authors'           => $data['authors'],
            'abstract_title'    => $data['abstract_title'],
            'role'              => ucwords($data['presentation']),
            'user'              => $user,
        ];

        $email = new AbstractNotificationMail($dataMail);
        Mail::to($user->email)->send($email);

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
        $writer->setColumnWidth(20, 1); // date
        $writer->setColumnWidth(30, 2); // email
        $writer->setColumnWidth(10, 3); // title
        $writer->setColumnWidth(40, 4); // name
        $writer->setColumnWidth(40, 5); // affiliation
        $writer->setColumnWidth(15, 6); // country
        $writer->setColumnWidth(20, 7); // presentation
        $writer->setColumnWidth(40, 8); // topic
        $writer->setColumnWidth(25, 9); // authors
        $writer->setColumnWidth(50, 10); // abstract title
        $writer->setColumnWidth(45, 11); // remarks

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
        $namaKolom = ['Date', 'Email','Title','Name', 'Affiliation','Country','Presentation', 'Topic', 'Authors', 'Abstract Title', 'Remarks'];
        $header = WriterEntityFactory::createRowFromArray($namaKolom, $styleHeader);
        $writer->addRow($header);
        $abstractData = $this->getAbstract();
        $styleNumber = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::CENTER)->build();
        $styleLeft = (new StyleBuilder())->setBorder($border)->setCellAlignment(CellAlignment::LEFT)->build();

        foreach ($abstractData as $row) {
            $tgl = $row->created_at->format('d-m-Y');
            $remarks = $row->is_presentation ? 'Abstract with Presentation Only' : 'Abstract with Full Paper Submission';
            $baris = [
                    WriterEntityFactory::createCell($tgl, $styleNumber),
                    WriterEntityFactory::createCell($row->email, $styleLeft),
                    WriterEntityFactory::createCell($row->title, $styleNumber),
                    WriterEntityFactory::createCell($row->fullname, $styleLeft),
                    WriterEntityFactory::createCell($row->affiliation, $styleLeft),
                    WriterEntityFactory::createCell($row->country, $styleNumber),
                    WriterEntityFactory::createCell($row->presentation, $styleNumber),
                    WriterEntityFactory::createCell($row->topic, $styleLeft),
                    WriterEntityFactory::createCell($row->authors, $styleLeft),
                    WriterEntityFactory::createCell($row->abstract_title, $styleLeft),
                    WriterEntityFactory::createCell($remarks, $styleLeft)];
                    $writer->addRow(WriterEntityFactory::createRow($baris));
        }
        $writer->close();
    }
}
