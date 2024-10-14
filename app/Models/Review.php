<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'reviews';
    protected $primaryKey = 'id';

     public function product()
    {      
        return $this->hasone('App\Models\Product', 'id', 'product_id');
    }
     public function userdata()
    {      
        return $this->hasone('App\Models\User', 'id', 'user_id');
    }
}
