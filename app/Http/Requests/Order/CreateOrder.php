<?php

namespace App\Http\Requests\Order;

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
        // TODO: check use products grids, defect can be attached to device
        return [
            "devices" => "array|required",
            "devices.*.id" => "required|numeric|exists:devices,id",
            "devices.*.defects" => "array",
            "devices.*.defects.*" => "exists:defects,id",
            "devices.*.condition" => "required|numeric|exists:conditions,id"
        ];
    }
}
