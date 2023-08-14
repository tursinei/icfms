<?php

namespace App\Services;

use App\Http\Requests\StorePaymentRequest;
use App\Mail\PaymentNotificationMail;
use App\Models\Documents;
use App\Models\DocumentsUser;
use App\Models\Payments;
use App\Models\User;
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


class DocumentService
{
    const DIRDOCUMENT = 'documents';

    public function getDirDocuments()
    {
        return self::DIRDOCUMENT;
    }

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
            $data['path_file'] = $dirUpload.'/'.$nameFileInServer;
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

    private function getDocuments($idUser = false)
    {
        return Documents::when($idUser, function ($query) use ($idUser) {
            return $query->join('document_user', 'document_user.document_id', 'documents.id')
            ->where('user_id', $idUser)->select('documents.*','user_id');
        })->with(['documentUser', 'documentUser.user'])->orderBy('documents.created_at', 'DESC')->get();
    }

    public function listTable()
    {
        $idCek = Session::get('icfms_tipe_login') == 1 ? false :Auth()->user()->id;
        $data = $this->getDocuments($idCek);
        return DataTables::of($data)->addColumn('action', function ($row) {
            return '<button class="btn btn-danger btn-xs btn-delete" data-id="' . $row->id. '"
                title="Delete Payment"><i class="fa fa-trash"></i></button>';
        })->addColumn('namaLink',function($row){
            return '<a href="'.route('documents.show',['document' => $row->id]).'?c='.csrf_token().'" target="_blank">'.$row->nama.'</a>';
        })->addColumn('btnView',function($row){
            return '<a class="btn btn-info btn-xs" href="' . route('documents.show', ['document' => $row->id]) . '?c=' . csrf_token() . '" target="_blank">View</a>';
        })->addColumn('upload_at',function($row){
            return date('Y-m-d H:i:s', strtotime($row->created_at.' +8 hours'));
        })->addColumn('users',function($row){
            $emails = array_map(function($i){
                return $i['user']['email'];
            }, $row->documentUser->toArray());
            if(count($emails) <= 1){
                return $emails[0] ?? '';
            }
            $li = '<li>' . $emails[0] . '</li><li>' . $emails[1] . '</li>';
            if(count($emails) == 2){
                return '<ol>' . $li . '</ol>';
            }
            $li .= '<li class="text-blue"><a class="span-more" href="#">More...</a></li>';
            $olMore = '<li>'.implode('</li><li>',$emails).'</li>';
            return '<ol>'.$li.'</ol><ol class="hide">'.$olMore.'</ol>';
        })->rawColumns(['action', 'users', 'namaLink', 'btnView', 'upload_at'])->make(true);
    }

    public function store(array $data)
    {
        $users = $data['user'] ?? [];
        $filePdf = $data['path_file'];

        unset($data['user']);
        unset($data['path_file']);

        $document = Documents::updateOrCreate(['id' => $data['id']],$data);

        if(!is_null($filePdf)){
            $name = $document->id.'.'.date('ymdhis').'.'.$filePdf->extension();
            $filePdf->storeAs('public/'.self::DIRDOCUMENT,$name);
            $document->path_file = $name;
            $document->save();
        }

        $this->saveDocumentUser($document->id,$users);

        return $document;
    }

    public function userByDocument($docId)
    {
        return DocumentsUser::whereUserId($docId)->pluck('user_id','id')->toArray();
    }

    private function saveDocumentUser($idDocument, array $users)
    {
        $usersDocument = $this->userByDocument($idDocument);
        $insert        = [];
        $delete        = [];
        $update        = [];

        $skip = array_intersect($usersDocument, $users);

        $skipKey = array_keys($skip);
        // dd($skipKey,$usersDocument);
        $ins = array_diff($users, $usersDocument); // get id users baru
        // remove id yg masih digunakan
        foreach ($skipKey as $key) {
            unset($usersDocument[$key]);
        }

        $gantiIdJurusan = array_diff($usersDocument, $users);
        $idWithDefault  = array_fill_keys(array_keys($gantiIdJurusan), 0);
        // dd($usersDocument,$idWithDefault,$users,$ins);

        foreach ($ins as $key => $idBaru) {
            $keyBaru = array_search(0, $idWithDefault);
            if (!$keyBaru) {
                break;
            }
            unset($ins[$key]);
            $idWithDefault[$keyBaru] = $idBaru;
        }

        foreach ($idWithDefault as $key => $value) {
            if ($value == 0) {
                continue;
            }
            $update[$key] = $value;
            unset($idWithDefault[$key]);
        }

        foreach ($ins as $value) {
            $insert[] = [
                'document_id' => $idDocument,
                'user_id'    => $value
            ];
        }

        foreach ($idWithDefault as $key => $value) {
            if ($value == 0) {
                $delete[] = $key;
            }
        }
        // dd($insert, $update, $delete);
        if (count($update)) {
            foreach ($update as $key => $val) {
                DocumentsUser::whereId($key)->update(['user_id' => $val]);
            }
        }

        if (count($delete)) {
            DocumentsUser::whereIn('id', $delete)->delete();
        }

        if (count($insert)) {
            DocumentsUser::insert($insert);
        }
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

        $fullpaperData = $this->getDocuments();
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
