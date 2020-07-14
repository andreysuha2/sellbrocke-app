<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Category as CategoryResource;

class CategoriesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = "categories";

    public function toArray($request)
    {
        return $this->collection->map(function ($category) {
            return new CategoryResource($category);
        });
    }
}
