<?php

namespace App\Http\Requests\ProductGrid;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductGrid extends FormRequest
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
            "thumbnail" => "exclude_if:type,size|required|image|max:100",
            "name" => "required",
            "type" => "required|in:carrier,size",
            "slug" => "required|unique:products_grids,slug|alpha_dash"
        ];
    }
}
