<?php

namespace App\Http\Requests\Order;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;

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
        $upsRules = [
            "shipment.shipFrom.Name" => "required",
            "shipment.shipFrom.AttentionName" => "required",
            "shipment.shipFrom.Phone.Number" => "required",
            "shipment.shipFrom.Address.AddressLine" => "required",
            "shipment.shipFrom.Address.City" => "required",
            "shipment.shipFrom.Address.StateProvinceCode" => "required",
            "shipment.shipFrom.Address.PostalCode" => "required",
            "shipment.shipFrom.Address.CountryCode" => "required",
            "shipment.service.Code" => "required",
            "shipment.package.Packaging.Code" => "required",
            "shipment.package.PackageWeight.UnitOfMeasurement.Code" => "required",
            "shipment.package.PackageWeight.Weight" => "required|numeric"
        ];

        $fedexRules = [
            "shipment.shipperAddress.line1" => "required",
            "shipment.shipperAddress.city" => "required",
            "shipment.shipperAddress.state_code" => "required",
            "shipment.shipperAddress.postal_code" => "required",
            "shipment.shipperAddress.country_code" => "required",
            "shipment.shipperContact.company_name" => "required",
            "shipment.shipperContact.email" => "required",
            "shipment.shipperContact.person_name" => "required",
            "shipment.shipperContact.phone" => "required",
            "shipment.weight" => "required"
        ];

        $shipmentRules = $this->shipment["type"] === "UPS" ? $upsRules : $fedexRules;

        return array_merge([
            "devices" => "array|required",
            "devices.*.id" => "required|numeric|exists:devices,id",
            "devices.*.defects" => "array",
            "devices.*.defects.*" => "exists:defects,id",
            "devices.*.condition" => "required|numeric|exists:conditions,id",
            "devices.*" => [ function($attribute, $value, $fail) {
                $device = Device::find($value["id"]);
                if($device) {
                    if(isset($value["defects"])) $this->checkDefects($device, $value, $fail, $attribute);
                }
            } ],
            "shipment.type" => "in:FEDEX,UPS"
        ],  $shipmentRules);
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
