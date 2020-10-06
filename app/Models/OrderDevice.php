<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDevice extends Model
{
    protected $table = "order_device";

    public function condition() {
        return $this->belongsTo("App\Models\Condition", "condition_id");
    }

    public function order() {
        return $this->belongsTo("App\Models\Order", "order_id");
    }

    public function device() {
        return $this->belongsTo("App\Models\Device", "device_id");
    }

    public function defects() {
        return $this->belongsToMany("App\Models\Defect", "order_device_defect", "order_device_id", "defect_id");
    }

    public function products_grids() {
        return $this->belongsToMany("App\Models\ProductGrid", "order_device_product_grid", "order_device_id", "product_grid_id");
    }

    public function getDiscountedPriceAttribute() {
        $defectsPriceReduction = $this->defects->sum("price_reduction");
        $discountedPercent = $this->device->company->price_reduction + $defectsPriceReduction + $this->condition->discount_percent;
        $ratio = (100 - $discountedPercent) / 100;
        $price = round($this->device->base_price * $ratio, 2);
        return $price > 0 ? $price : 0;
    }
}
