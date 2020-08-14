<?php

namespace App\Http\Resources\Categories;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Categories\Category as CategoryResource;

class CategoriesCollection extends ResourceCollection
{
    private $withChildren;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = "categories";

    public function __construct($resource, $withChildren = false)
    {
        parent::__construct($resource);
        $this->withChildren = $withChildren;
    }

    public function toArray($request)
    {
        return $this->collection->map(function ($category) {
            return new CategoryResource($category, $this->withChildren, $this->withChildren);
        });
    }
}
