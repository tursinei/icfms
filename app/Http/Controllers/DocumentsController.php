<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Documents;
use App\Models\Payments;
use App\Models\User;
use App\Services\DocumentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class DocumentsController extends Controller
{

    private DocumentService $service;

    public function __construct(DocumentService $serviceParam)
    {
        $this->service = $serviceParam;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            return $this->service->listTable();
        }
        if(Session::get('icfms_tipe_login') == 0){
            return view('pages.documentsUser');
        }
        return view('pages.documents');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $listEmail = User::where('is_admin', 0)->pluck('email', 'id');
        return view('pages.modal.documentModal',compact('listEmail'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentRequest $request)
    {
        $data = $request->validated();
        $payment = $this->service->store($data);
        return response()->json([
            'message' => ($payment ? 'Success' : 'Failed') . ' to store data'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Documents $document)
    {
        $csrf = $request->get('c');

        if($request->session()->token() != $csrf){
            throw new TokenMismatchException();
        }

        $path = storage_path('app/public/'.$this->service->getDirDocuments().'/'.$document->path_file);

        if(File::exists($path)){
            return response()->file($path,[
                'Content-disposition'=> 'filename='.$document->nama.'.'.pathinfo($path)['extension']
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Payments  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Documents $document)
    {
        $path = storage_path('app/documents/'.$document->path_file);

        if(File::exists($path)){
            File::delete($path);
        }
        $isDelete = $document->delete();
        return response()->json([
            'status' => $isDelete,
            'message' => ($isDelete ? 'Success' : 'Failed') . ' to delete data'
        ]);
    }
}
