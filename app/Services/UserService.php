<?php

namespace App\Services;

use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class UserService
{

    public function listUser($tipeUser){
        $select = ['users.email', 'users.id','a.*'];
        $isAdmin = $tipeUser == 1;
        if($isAdmin){
            $select = ['name', 'a.affiliation', 'email', 'a.phonenumber','id'];
        }
        $data = User::join('users_details AS a', 'id', 'a.user_id')->where('users.is_admin',$tipeUser)
                ->orderBy('users.name')->get($select);
        return DataTables::of($data)->addColumn('action', function ($row) use($isAdmin){
            $btnEditOrDel = $isAdmin ? '<a class="btn btn-primary btn-xs" data-id="'.$row->id.'" title="Edit Data">
                    <i class="fa fa-pencil"></i></a>' : '';
            $titleDel = $isAdmin ? 'Delete User' : 'Delete Participants';
            return $btnEditOrDel.'&nbsp;<button data-id="' . $row->id . '" class="btn btn-danger btn-xs btn-hapus"
                title="'.$titleDel.'"><i class="fa fa-trash-o"></i></button>';
        })->rawColumns(['action'])->make(true);
    }

}
