<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

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
        $categoryId = $this->route("category")->id;

        return [
            "name" => "sometimes|required",
            "slug" => "sometimes|required|unique:categories",
            "thumbnail" => "sometimes|required|image|max:100",
            "attach_defects" => "sometimes|present|array",
            "attach_defects.*" => "sometimes|numeric|exists:defects,id|unique:category_defect,defect_id,NULL,id,category_id,$categoryId",
            "detach_defects" => "sometimes|present|array",
            "detach_defects.*" => "sometimes|numeric"
        ];
    }

    protected function prepareForValidation()
    {
        if($this->request->has("attach_defects")) {
            $this->merge([ "attach_defects" => json_decode($this->attach_defects) ]);
        }
        if($this->request->has("detach_defects")) {
            $this->merge([ "detach_defects" => json_decode($this->detach_defects) ]);
        }
    }
}
