<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Models\Announcements;
use App\Models\Mtopic;
use App\Services\AbstractService;
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
        // $target = [
        //     'oral,poster,audience' => 'Participants (Oral, Poster, Audience)',
        //     'keynote speaker' => 'Only Keynote Speaker'
        // ];
        $target = array_combine(AbstractService::ROLES, AbstractService::ROLES);

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
        $mail  = $service->emails($request->input('target'));
        $mails = array_filter($mail);
        $selectedMail   = array_rand($mails); // random pick
        $names  = explode('#', $mails[$selectedMail]); // 0 firstname, 1 fullname

        if (strpos($body, AnnouncementService::LABEL_ABSTRACT) OR
                strpos($body, AnnouncementService::LABEL_AFFILIATION)) {
            $abstractData = $service->getAbstractPresentation($request->input('target'));
            $absPesentration = $abstractData[$selectedMail];
            $body   = str_replace($service::LABEL_ABSTRACT, $absPesentration['abstract'], $body);
            $body   = str_replace($service::LABEL_PRESENTATION, $absPesentration['presentation'], $body);
        }
        $body   = str_replace($service::LABEL_FULL, $names[1], $body);
        $body   = str_replace($service::LABEL_AFFILIATION, $names[2], $body);
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
