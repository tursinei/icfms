<?php

namespace App\Services;

use App\Http\Requests\StorefullPaperRequest;
use App\Models\FullPaper;
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
    //full paper tanpa user
    public function listFullpaper()
    {
        $data = FullPaper::join('abstract_file AS a', 'a.abstract_id', 'full_paper.abstract_id')
            ->join('m_topic AS t', 't.topic_id', 'a.topic_id')
            ->join('users AS u', 'u.id', 'full_paper.user_id')
            ->orderBy('full_paper.created_at', 'DESC')
            ->get(['paper_id', 'u.name as fullname' ,'t.name as topic','full_paper.created_at','presenter','presentation', 'authors', 'a.paper_title as title']);
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
        $title = str_replace(' ','_', $titleFile).'.'.$file->getClientOriginalExtension();
        $data['file_name'] = $title;
        $data['file_path'] = $dirUpload.'/'.$nameFileInServer;
        $data['size'] = $file->getSize();
        $data['extensi'] = $file->getClientOriginalExtension();
        $file->move($dirUpload,$nameFileInServer);
        return FullPaper::create($data);
    }
}
