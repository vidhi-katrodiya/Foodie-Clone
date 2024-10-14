<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletePayment extends Model
{
    use HasFactory;
    protected $table = 'complete_payment';
    protected $primaryKey = 'id';
}
