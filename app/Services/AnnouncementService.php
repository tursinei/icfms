<?php

namespace App\Services;

use App\Http\Requests\StoreAnnouncementRequest;
use App\Jobs\EmailAnnouncementJob;
use App\Models\Announcements;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;


class AnnouncementService
{
    public static $LABEL_FIRST = '{#firstname#}';
    public static $LABEL_FULL = '{#fullname#}';

    public function listUser($tipeUser)
    {
        $select = ['users.email', 'users.id', 'a.*'];
        $isAdmin = $tipeUser == 1;
        if ($isAdmin) {
            $select = ['name', 'a.affiliation', 'email', 'a.phonenumber', 'id'];
        }
        $data = User::join('users_details AS a', 'id', 'a.user_id')->where('users.is_admin', $tipeUser)
            ->orderBy('users.name')->get($select);
        return DataTables::of($data)->addColumn('action', function ($row) use ($isAdmin) {
            $btnEditOrDel = $isAdmin ? '<a class="btn btn-primary btn-xs btn-edit" data-id="' . $row->id . '" title="Edit Data">
                    <i class="fa fa-pencil"></i></a>' : '';
            $titleDel = $isAdmin ? 'Delete User' : 'Delete Participants';
            return $btnEditOrDel . '&nbsp;<button data-id="' . $row->id . '" class="btn btn-danger btn-xs btn-hapus"
                title="' . $titleDel . '"><i class="fa fa-trash-o"></i></button>';
        })->rawColumns(['action'])->make(true);
    }

    public function simpan(StoreAnnouncementRequest $request)
    {
        $data = $request->validated();
        $file = $request->file('attachment');
        unset($data['attachment']);
        if ($request->hasFile('attachment')) {
            $dirUpload = 'file_announcement';
            $nameFileInServer = 'announcement-' . date("ymdHis") . '.' . $file->getClientOriginalExtension();
            $titleFile = $file->getClientOriginalName();
            $title = str_replace(' ', '_', $titleFile) . '.' . $file->getClientOriginalExtension();
            $data['file_name'] = $title;
            $data['file_path'] = $dirUpload . '/' . $nameFileInServer;
            $data['file_mime'] = $file->getClientMimeType();
            $file->move($dirUpload, $nameFileInServer);
        }

        $targets = $this->emails($request->input('target'));
        $mailsSendTo = array_keys($targets);
        $data['sendto'] = json_encode($mailsSendTo);
        $announce = Announcements::create($data);
        if ($request->hasFile('attachment')) {
            $urlExtentension = $announce->id . '.' . $file->getClientOriginalExtension();
            $announce->attachment = route('announcement.file', ['id' => $urlExtentension]);
            $data['attachment'] = $announce->attachment;
        }
        $tmpBody = $data['isi_email'];
        foreach ($targets as $mail => $name) {
            $names = explode('#', $name); // 0: firstname, 1 : fullname
            $data['isi_email'] = $tmpBody;
            $body              = str_replace(self::$LABEL_FIRST, $names[0], $data['isi_email']);
            $data['isi_email'] = str_replace(self::$LABEL_FULL, $names[1], $body);
            dispatch(new EmailAnnouncementJob($data, $mail));
        }
        return $announce->save();
    }

    public function emails($target)
    {
        $presentations = explode(',', $target);
        return User::select('users.email', DB::raw("CONCAT(users_details.firstname,'#',users.name) AS nama"))
            ->join('abstract_file', 'users.id', 'abstract_file.user_id')
            ->join('users_details', 'users.id', 'users_details.user_id')
            ->whereIn('presentation', $presentations)->distinct()->pluck('nama', 'email')->toArray();
    }

    public function getFile($id)
    {
        $data = Announcements::find($id);
        $path = public_path($data->file_path);
        if (File::exists($path)) {
            return response()->download($path, $data->file_name);
        } else {
            throw new Exception("File Not Found", 1);
        }
    }
}
