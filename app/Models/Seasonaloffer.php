<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seasonaloffer extends Model
{
    use HasFactory;
    protected $table = 'seasonal_offer';
    protected $primaryKey = 'id';
}
