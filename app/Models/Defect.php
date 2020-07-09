<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    protected $table = "defects";

    protected $fillable = [ "name", "description", "price_reduction" ];
}
