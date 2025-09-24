<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FullPaper extends Model
{
    protected $table = 'full_paper';

    protected $primaryKey = 'paper_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $hidden = [];

    protected $fillable = [
        'abstract_id',
        'file_name',
        'file_path',
        'extensi',
        'size',
        'user_id',
    ];

    protected $guarded = [];

    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'abstract_id' => 'integer',
        'file_name' => 'string',
        'file_path' => 'string',
        'extensi' => 'string',
        'size' => 'double',
        'user_id' => 'integer'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function abstract(){
        return $this->belongsTo(AbstractFile::class, 'abstract_id', 'abstract_id');
    }

    protected function scopePeriode($query, $year = null)
    {
        if (is_null($year)) {
            $year = date('Y');
        }
        return $query->whereBetween('full_paper.created_at', [$year . '-1-1', $year . '-12-31']);
    }
}
