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

    public function log() {
        return $this->hasMany("App\Models\OrderLog", "order_id");
    }

    public function getPricesAttribute() {
        $base = $this->devices->sum(function ($orderDevice) {
            return $orderDevice->device->base_price;
        });
        $discounted = $this->devices->sum("discounted_price");
        return [ "base" => round($base, 2), "discounted" => round($discounted, 2) ];
    }
}
