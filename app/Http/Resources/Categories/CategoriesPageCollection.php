<?php

namespace App\Http\Resources\Categories;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Categories\CategoryPage as CategoryPageResource;

class CategoriesPageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($category) {
            return new CategoryPageResource($category);
        })->toArray();
    }
}
