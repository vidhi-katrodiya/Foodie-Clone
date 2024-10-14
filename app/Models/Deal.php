<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;
    protected $table = 'deals';
    protected $primaryKey = 'id';
     
    public function offer()
    {      
        return $this->hasone('App\Models\Offer', 'id', 'offer_id');
    }
}
