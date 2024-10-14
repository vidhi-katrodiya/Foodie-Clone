<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Optionvalues extends Model
{
    use HasFactory;
    protected $table = 'option_values';
    protected $primaryKey = 'id';
}
