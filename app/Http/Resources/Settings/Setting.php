<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Resources\Json\JsonResource;

class Setting extends JsonResource
{
    public static $wrap = "setting";

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "group" => $this->group,
            "settings" => [
                "key" => $this->key,
                "value" => $this->value,
            ]
        ];
    }
}
