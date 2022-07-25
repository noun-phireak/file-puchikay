<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resize extends Model
{	
    use SoftDeletes;
    protected $table = 'file_resizes';

}