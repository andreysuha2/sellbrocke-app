<?php

namespace App\Http\Requests\Merchant;

use App\Models\Merchant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePassword extends FormRequest
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
        $merchant = $this->route("merchant");

        return [
            "old_password" => [
                "required",
                function($attribute, $value, $fail) use ($merchant) {
                    if(!Hash::check($value, $merchant->password)) {
                        $fail("Current password is invalid");
                    }
                }
            ],
            "new_password" => "required|min:8",
            "confirm_password" => "same:new_password"
        ];
    }
}
