<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductGrid extends JsonResource
{
    public static $wrap = "productGrid";
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $hasThumbnail = $this->type === "carrier";
        $thumbnailRecord = $hasThumbnail ? $this->attachment("thumbnail") : null;
        $thumbnailPath = $thumbnailRecord ? $thumbnailRecord->url : null;

        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "type" => $this->type,
            "thumbnail" => $this->when($hasThumbnail, $thumbnailPath)
        ];
    }
}
