<?php

namespace App\Models;

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
        'presentation',
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
        'presentation' => 'string'
    ];

    public static function affiliations()
    {
        $afiliations =  [
            'Riken', "Institut Teknologi Sepuluh Nopember",
            "Universitas Padjadjaran", "Institut Teknologi Bandung",
            "Universitas Gadjah Mada", "Universitas Indonesia","Another"
        ];
        return array_combine($afiliations,$afiliations);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
