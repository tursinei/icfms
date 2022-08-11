<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcements;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;

class AnnouncementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $target = [
            'oral,poster,audience' => 'Participants (Oral, Poster, Audience)',
            'keynote speaker' => 'Only Keynote Speaker'
        ];

        return view('pages.announcement', compact('target'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        $title = 'Preview Announcement Email';
        $service = new AnnouncementService();
        $titleEmail = $request->input('title');
        $body   = $request->input('isi_email');
        $mails  = $service->emails($request->input('target'));
        $keys   = array_rand($mails); // random pick
        $names  = explode('#', $mails[$keys]); // 0 firstname, 1 fullname
        $body   = str_replace($service::$LABEL_FIRST, $names[0], $body);
        $body   = str_replace($service::$LABEL_FULL, $names[1], $body);
        return view('pages.modal.previewAnnouncementModal', compact('title', 'titleEmail', 'body'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnnouncementRequest $request)
    {
        $service = new AnnouncementService();
        $isSuccess = $service->simpan($request);
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Successfully sent email' : 'Failed to send email')
            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  String  $id
     * @return \Illuminate\Http\Response
     */
    public function attachment($id)
    {
        $exp = explode('.', $id); // id dgn extension
        $service = new AnnouncementService();
        $service->getFile($exp[0]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Announcements  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcements $id)
    {
        $isSuccess = $id->delete();
        return response()->json([
            'status' => $isSuccess,
            'message' => [
                'head' => ($isSuccess ? 'Success' : 'Failed').' to remove data'
            ]
        ], 200);
    }
}
