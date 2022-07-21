<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcements extends Model
{
    protected $table = 'announcements';

    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = true;

    protected $hidden = [];

    protected $fillable = [
        'id',
        'title',
        'target',
        'sendto',
        'isi_email',
        'attachment',
        'file_name',
        'file_path',
        'file_mime',
    ];

    protected $guarded = [];

    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'target' => 'string',
        'sendto' => 'string',
        'isi_email' => 'string',
        'attachment' => 'string',
        'file_name' => 'string',
        'file_path' => 'string',
        'file_mime' => 'string',
    ];
}
