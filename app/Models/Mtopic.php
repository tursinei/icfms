<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mtopic extends Model
{
    protected $table = 'm_topic';

    protected $primaryKey = 'topic_id';

    public function abstract(){
       return $this->belongsTo(AbstractFile::class, 'topic_id','topic_id');
    }
}
