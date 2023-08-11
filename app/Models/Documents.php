<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{

    use HasFactory;

    protected $table = 'documents';

    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = true;

    protected $hidden = [];

    protected $fillable = [
        'id',
        'nama',
        'path_file',
    ];

    protected $guarded = [];

    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'id' => 'integer',
        'nama' => 'string',
        'path_file' => 'string',
        'created_at' => 'date:d-m-Y H:i:s'
    ];

    public function documentUser()
    {
        return $this->hasMany(DocumentsUser::class,'document_id','id');
    }

}
