<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sysinfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sysinfo';

    protected $cast = [
        'id' => 'integer',
        'tipe'  => 'string',
        'key'   => 'string',
        'value' => 'string'
    ];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tipe','key','value'];
}
