<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    public $hidden = [ "order_id" ];

    public function order() {
        return $this->belongsTo("App\Models\Order");
    }
}
