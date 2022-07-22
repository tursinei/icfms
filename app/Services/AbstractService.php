<?php

namespace App\Services;

use App\Http\Requests\StoreAbstractFileRequest;
use App\Models\AbstractFile;
use Yajra\DataTables\Facades\DataTables;

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

        try {
            $file->move($dirUpload,$nameFileInServer);
            return AbstractFile::create($data);
        } catch (\Exception $th) {
            throw $th->getMessage();
        }

    }
}
