<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderData extends Model
{
    use HasFactory;
    protected $table = 'order_data';
    protected $primaryKey = 'id';
    public function productdata(){
         return $this->hasone('App\Models\Product', 'id', 'product_id');
    }
    
    public function userdata(){
         return $this->hasone('App\Models\User', 'id', 'user_id');
    }

    public function Orderdetail(){
         return $this->hasone('App\Models\Order', 'id', 'order_id');
    }
}
