<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentNotif extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_notif';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'paymentnotif_id';
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'tgl_payment'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['attribut','nominal','tgl_payment','invoice_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'attribut'  => 'json',
        'nominal'   => 'double',
        'tgl_payment' => 'date:d-m-Y',
        'invoice_id' => 'integer',
    ];

    public function invoice()
    {
        return $this->belongsTo(InvoiceNotif::class,'invoice_id','invoice_id');
    }
}
