<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureProduct extends Model
{
    use HasFactory;
    protected $table = 'feature_product';
    protected $primaryKey = 'id';
    
    public function productdata(){
    	 return $this->hasone('App\Models\Product', 'id', 'product_id');
    }
}
