<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGrid extends Model
{
    protected $table = "products_grids";

    protected $fillable = [ "name", "type", "slug" ];

    public function devices() {
        return $this->belongsToMany("App\Models\Device", "device_product_grid", "product_grid_id", "device_id");
    }
}
