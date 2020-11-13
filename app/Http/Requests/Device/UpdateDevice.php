<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDevice extends FormRequest
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
        $deviceId = $this->route("device")->id;

        return [
            "thumbnail" => "sometimes|required|image|max:300",
            "name" => "sometimes|required",
            "base_price" => "sometimes|required|numeric",
            "company" => "sometimes|required|exists:companies,id",
            "attach_categories" => "sometimes|required|present|array",
            "attach_categories.*" => "sometimes|numeric|exists:categories,id|unique:category_device,category_id,NULL,id,device_id,$deviceId",
            "detach_categories" => "sometimes|required|present|array",
            "detach_categories.*" => "sometimes|numeric",
            "products_grids" => "sometimes|required|present|array",
            "products_grids.*" => "sometimes|numeric|exists:products_grids,id",
            "slug" => "sometimes|required|unique:devices,slug,NULL,id,company_id,$this->company_id|alpha_dash"
        ];
    }

    protected function prepareForValidation()
    {
        if($this->request->has("attach_categories")) {
            $this->merge([ "attach_categories" => json_decode($this->attach_categories) ]);
        }
        if($this->request->has("detach_categories")) {
            $this->merge([ "detach_categories" => json_decode($this->detach_categories) ]);
        }
        if($this->request->has("products_grids")) {
            $this->merge([
                "products_grids" => json_decode($this->products_grids)
            ]);
        }
    }
}
