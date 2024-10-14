<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sepicalcategories extends Model
{
    use HasFactory;
    protected $table = 'sepical_category';
    protected $primaryKey = 'id';
}
