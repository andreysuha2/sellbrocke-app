<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    // TODO: make softdelete
    protected $table = "defects";

    protected $fillable = [ "name", "description", "price_reduction" ];
}
