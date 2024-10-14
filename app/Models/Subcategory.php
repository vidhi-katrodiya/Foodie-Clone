<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
    protected $table = 'subcategory';
    protected $primaryKey = 'id';

    public function category()
    {      
        return $this->hasone('App\Models\Categories', 'id', 'cat_id');
    }
}
