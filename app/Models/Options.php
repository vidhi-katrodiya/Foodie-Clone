<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
    use HasFactory;
    protected $table = 'options';
    protected $primaryKey = 'id';

    public function optionlist()
    {      
        return $this->hasmany('App\Models\Optionvalues', 'option_id', 'id');
    }
}
