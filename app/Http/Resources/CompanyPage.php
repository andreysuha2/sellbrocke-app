<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyPage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $logoRecord = $this->attachment("logo");
        $logoPath = $logoRecord ? $logoRecord->url : null;

        return [
            "id" => $this->id,
            "name" => $this->name,
            "logo" => $logoPath,
            "slug" => $this->slug
        ];
    }
}
