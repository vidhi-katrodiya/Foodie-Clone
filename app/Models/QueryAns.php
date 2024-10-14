<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryAns extends Model
{
    use HasFactory;
    protected $table = 'query_ans_question';
    protected $primaryKey = 'id';

    public function query_id(){
    	 return $this->hasone('App\Models\QueryTopic', 'id', 'topic_id');
    }
}
    