<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'id';

    public function categoryls()
    {      
        return $this->hasone('App\Models\Categories', 'id', 'category');
    }
    public function subcategoryls()
    {      
        return $this->hasone('App\Models\Categories', 'id', 'subcategory');
    }
    public function brandls()
    {      
        return $this->hasone('App\Models\Brand', 'id', 'brand');
    }
     public function optionls()
    {      
        return $this->hasone('App\Models\ProductOption', 'product_id', 'id');
    }
      public function Attributls()
    {      
        return $this->hasone('App\Models\ProductOption', 'product_id', 'id');
    }
    public function rattingdata()
    {      
        return $this->hasmany('App\Models\Review', 'product_id', 'id');
    }
}
