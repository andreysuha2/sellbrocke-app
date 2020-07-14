<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Category extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = "category";

    public function toArray($request)
    {
        $thumbnail = $this->attachment("thumbnail");
        return [
            "id" => $this->id,
            "name" => $this->name,
            "thumbnail" => $thumbnail ? $thumbnail->url : null,
            "description" => $this->description,
            "slug" => $this->slug
        ];
    }
}
