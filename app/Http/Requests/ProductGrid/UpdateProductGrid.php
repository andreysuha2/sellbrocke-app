<?php

namespace App\Http\Requests\ProductGrid;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductGrid extends FormRequest
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
            "thumbnail" => "sometimes|exclude_if:type,size|required|image|max:100",
            "name" => "sometimes|required",
            "type" => "sometimes|required|in:carrier,size",
            "slug" => "sometimes|required|unique:products_grids,slug|alpha_dash"
        ];
    }
}
