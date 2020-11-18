<?php

namespace App\Models;

use App\Services\SettingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        $quotes = SettingService::getParametersByGroup("quotes");

        // First thing first we need to get device price сonsidering the company reducing percents.
        $companyReductionRatio = (100 - $this->device->company->price_reduction) / 100;
        $discountedAmount = round($this->device->base_price * $companyReductionRatio, 2);

        if ($discountedAmount >= $quotes["MAXIMUM_BASE_PRICE"]) {
            $totalAmount = $quotes["FIXED_PRICE"];
        } else {
            $defectsPriceReduction = $this->defects->sum("price_reduction");
            if (count($this->defects) === 3) {
                $defectsPriceReduction = $defectsPriceReduction - $quotes["THREE_DEFECTS_REDUCE_PERCENT"];
            }

            if (count($this->defects) >= 4) {
                $defectsPriceReduction = $defectsPriceReduction - $quotes["FOUR_DEFECTS_REDUCE_PERCENT"];
            }

            // All defects percents amount + condition percents
            $discountedPercent = $defectsPriceReduction + $this->condition->discount_percent;
            $defectsConditionRatio = (100 - $discountedPercent) / 100;
            $totalAmount = round($discountedAmount * $defectsConditionRatio, 2);
        }

        return $totalAmount > 0 ? $totalAmount : 0;
    }
}
