<?php

namespace App\Http\Requests\Order;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CreateOrder extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "devices" => "array|required",
            "devices.*.id" => "required|numeric|exists:devices,id",
            "devices.*.defects" => "array",
            "devices.*.defects.*" => "exists:defects,id",
            "devices.*.condition" => "required|numeric|exists:conditions,id",
            "devices.*" => [ function($attribute, $value, $fail) {
                $device = Device::find($value["id"]);
                if($device) {
                    $this->checkProductsGrid($device, $value, $fail, $attribute);
                    $this->checkDefects($device, $value, $fail, $attribute);
                }
            } ]
        ];
    }

    private function checkProductsGrid($device, $input, $fail, $attribute) {
        if($device->use_products_grids) {
            if(!isset($input["productsGrids"])) $fail("$attribute device is used products grids");
            else {
                $productsGrids = $device->productsGrids()->whereIn("products_grids.id", $input["productsGrids"])->get();
                if($productsGrids->count() !== 2) $fail("$attribute, wrong products grids values");
                if(!$productsGrids->contains("type", "size")) $fail("$attribute, product grid size is missed");
                if(!$productsGrids->contains("type", "carrier")) $fail("$attribute, product grid size is missed");
            }
        }
    }

    private function checkDefects($device, $input, $fail, $attribute) {
        collect($input["defects"])->each(function ($defectId) use ($device, $fail, $attribute) {
            if(!$device->defects()->where("id", $defectId)->exists()) $fail("$attribute, device hasn't defect with id: $defectId");
        });
    }
}
