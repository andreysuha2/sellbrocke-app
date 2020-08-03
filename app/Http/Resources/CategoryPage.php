<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryPage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $thumbnailRecord = $this->attachment("thumbnail");
        $thumbnailPath = $thumbnailRecord ? $thumbnailRecord->url : null;

        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "name" => $this->name,
            "description" => $this->description,
            "thumbnail" => $thumbnailPath
        ];
    }
}
