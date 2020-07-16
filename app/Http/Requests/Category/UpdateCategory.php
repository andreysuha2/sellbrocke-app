<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategory extends FormRequest
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
            "slug" => "sometimes|required|unique:categories",
            "thumbnail" => "sometimes|required|image|max:100",
            "defects" => "sometimes|present|array",
            "defects.*" => "sometimes|numeric|exists:defects,id"
        ];
    }

    protected function prepareForValidation()
    {
        if($this->request->has("defects")) {
            $this->merge([ "defects" => json_decode($this->defects) ]);
        }
    }
}
