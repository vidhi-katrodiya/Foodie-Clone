<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
    use HasFactory;
    protected $table = 'attributes';
    protected $primaryKey = 'id';

     public function setname()
    {      
        return $this->hasone('App\Models\AttributeSet', 'id', 'att_set_id');
    }
}
