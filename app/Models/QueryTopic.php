<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryTopic extends Model
{
    use HasFactory;
    protected $table = 'question_query_topic';
    protected $primaryKey = 'id';

     public function Question()
    {      
        return $this->hasmany('App\Models\QueryAns', 'topic_id', 'id');
    }
}
