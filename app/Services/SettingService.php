<?php

namespace App\Services;

use App\Models\Sysinfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingService
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->setting as $key => $value) {
                Sysinfo::where('key','=',$key)->update(['value' => $value]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['message' => $th->getMessage()];
        }
    }
}

