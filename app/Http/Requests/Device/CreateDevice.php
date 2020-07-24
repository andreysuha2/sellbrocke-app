<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class CreateDevice extends FormRequest
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
            "thumbnail" => "required|image|max:100",
            "name" => "required",
            "base_price" => "required|numeric",
            "company" => "required|exists:companies,id",
            "categories" => "required|present|array",
            "categories.*" => "numeric|exists:categories,id",
            "slug" => "required|unique:devices,slug,NULL,id,company_id,$this->company_id|alpha_dash",
            "products_grids" => "array",
            "products_grids.*" => "numeric|exists:products_grids,id"
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            "categories" => json_decode($this->categories)
        ]);
        if($this->request->has("use_products_grids") && $this->use_products_grids) {
            $this->merge([
                "products_grids" => json_decode($this->products_grids)
            ]);
        }
    }
}
