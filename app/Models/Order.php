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
        return $this->belongsToMany("App\Models\Device", "device_order","order_id", "device_id");
    }

    public function getDevices() {
        $devices = $this->devices()->select(
            "devices.*",
                    "device_order.id as order_device_id",
                    "device_order.condition_id"
            )->get();
        $defects = Defect::where("device_order.order_id", $this->id)
                         ->join("order_device_defect", "defects.id", "=", "order_device_defect.defect_id")
                         ->join("device_order", "order_device_defect.order_device_id", "=", "device_order.id")
                         ->select("defects.*", "order_device_defect.order_device_id")
                         ->get();

        $conditions = Condition::all();

        $devices = $devices->map(function ($device) use ($defects, $conditions) {
           $device->defects = $defects->filter(function ($defect) use ($device) {
              return $defect->order_device_id === $device->order_device_id;
           })->values()->all();
           $device->condition = $conditions->find($device->condition_id);
           return $device;
        });

        return $devices;
    }
}
