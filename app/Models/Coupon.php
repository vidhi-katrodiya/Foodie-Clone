<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = 'coupon';
    protected $primaryKey = 'id';

    public function resdata(){
    	 return $this->hasone('App\Models\User', 'id', 'user_id');
    }
}
