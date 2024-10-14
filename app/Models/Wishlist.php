<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $table = 'wishlist';
    protected $primaryKey = 'id';

     public function productdata()
    {      
        return $this->hasone('App\Models\Product', 'id', 'product_id');
    }
     public function favresdata(){
    	 return $this->hasone('App\Models\User', 'id', 'res_id');
    }
}
