<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $hidden = [];

    protected $fillable = [
        'payment_id',
        'invoice_id',
        'file_name',
        'file_path',
        'currency',
        'nominal',
        'user_id',
    ];

    protected $guarded = [];

    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'file_name' => 'string',
        'file_path' => 'string',
        'currency' => 'string',
        'nominal' => 'double',
        'invoice_id' => 'integer',
        'user_id' => 'integer'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id', 'id');
    }
}
