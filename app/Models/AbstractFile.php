<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AbstractFile extends Model
{
    protected $table = 'abstract_file';

    protected $primaryKey = 'abstract_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $hidden = [];

    protected $fillable = [
        'abstract_id',
        'presenter',
        'presentation',
        'authors',
        'abstract_title',
        'paper_title',
        'abstract',
        'file_name',
        'file_path',
        'extensi',
        'size',
        'user_id',
        'topic_id',
        'is_presentation'
    ];

    protected $guarded = [];

    protected $dates = [
        'created_at'
    ];

    protected $casts = [
        'presenter' => 'string',
        'presentation' => 'string',
        'authors' => 'string',
        'abstract_title' => 'string',
        'paper_title' => 'string',
        'abstract' => 'string',
        'file_name' => 'string',
        'file_path' => 'string',
        'extensi' => 'string',
        'size' => 'double',
        'user_id' => 'integer',
        'topic_id' => 'integer',
        'is_presentation' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function topic()
    {
        return $this->belongsTo(Mtopic::class, 'topic_id', 'topic_id');
    }

    public function fullPaper(){
        return $this->hasMany(FullPaper::class,'abstract_id', 'abstract_id');
    }

    protected function scopePeriode(Builder $query, $year = null)
    {
        if (is_null($year)) {
            $year = date('Y');
        }
        return $query->whereBetween('created_at', [$year.'-1-1', $year.'-12-31']);
    }
}
