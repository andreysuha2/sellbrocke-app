<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [ "status" ];

    public function customer() {
        return $this->belongsTo("App\Models\Customer", "customer_id");
    }

    public function devices() {
        return $this->hasMany("App\Models\OrderDevice", "order_id");
    }
}
