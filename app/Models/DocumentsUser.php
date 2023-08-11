<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentsUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['document_id', 'user_id','id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function document()
    {
        return $this->belongsTo(Documents::class,'document_id','id');;
    }


}
