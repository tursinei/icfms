<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'invoice_id';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['attribut', 'invoice_number','currency','nominal','role',
                'abstract_title','user_id', 'tgl_invoice','payment_tgl',
                'status', 'nominal_rupiah', 'payment_fee', 'jenis',
                'payment_method', 'order_id','snap_token','status','keterangan'
                ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['tgl_invoice', 'payment_tgl'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'attribut'          => 'json',
        'invoice_number'    => 'string',
        'currency'          => 'string',
        'nominal'           => 'double',
        'role'              => 'json',
        'abastract_title'   => 'json',
        'tgl_invoice'       => 'date:d-m-Y',
        'user_id'           => 'integer',
        'payment_tgl'       => 'date:d-m-Y',
        'payment_fee'       => 'double',
        'payment_method'    => 'string',
        'status'            => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function userDetail()
    {
        return $this->belongsTo(UserDetail::class,'user_id','user_id');
    }
}
