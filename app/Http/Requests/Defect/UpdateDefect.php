<?php

namespace App\Http\Requests\Defect;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDefect extends FormRequest
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
            "description" => "string|nullable",
            "price_reduction" => "sometimes|required|numeric|between:0,100.00",
        ];
    }
}
