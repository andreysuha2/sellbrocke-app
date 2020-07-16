<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompany extends FormRequest
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
            "name" => "sometimes|required",
            "price_reduction" => "sometimes|required|numeric|between:0,100.00",
            "slug" => "sometimes|required|unique:companies|alpha_dash",
            "logo" => "sometimes|required|image|max:100"
        ];
    }
}
