<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model
{
    use HasFactory;
    protected $table = 'attribute_set';
    protected $primaryKey = 'id';

    public function attributelist()
    {      
        return $this->hasmany('App\Models\Attributes', 'att_set_id', 'id');
    }
}
