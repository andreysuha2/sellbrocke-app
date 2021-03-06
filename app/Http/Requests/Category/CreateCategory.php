<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategory extends FormRequest
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
            "name" => "required",
            "slug" => "required|unique:categories",
            "thumbnail" => "required|image|max:100",
            "defects" => "present|array",
            "defects.*" => "numeric|exists:defects,id"
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            "defects" => json_decode($this->defects)
        ]);
    }
}
