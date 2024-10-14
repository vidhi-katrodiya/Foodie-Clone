<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronSchedule extends Model
{
    use HasFactory;
    protected $table = 'update_cron';
    protected $primaryKey = 'id';
}
