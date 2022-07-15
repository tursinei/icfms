<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'users_details';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    public $timestamps = true;

    protected $hidden = [];

    protected $fillable = [
        'user_id',
        'title',
        'firstname',
        'midlename',
        'lastname',
        'affiliation',
        'address',
        'country',
        'secondemail',
        'phonenumber',
        'mobilenumber'
    ];

    protected $guarded = [];

    protected $dates = [];

    protected $casts = [
        'user_id' => 'integer',
        'title' => 'string',
        'firstname' => 'string',
        'midlename' => 'string',
        'lastname' => 'string',
        'affiliation' => 'string',
        'address' => 'string',
        'country' => 'string',
        'secondemail' => 'string',
        'phonenumber' => 'string',
        'mobilenumber' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
