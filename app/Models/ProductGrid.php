<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGrid extends Model
{
    protected $table = "products_grids";

    protected $fillable = [ "name", "type", "slug" ];
}
