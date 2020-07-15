<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Category extends JsonResource
{
    public static $wrap = "category";

    private $withChildren;

    private $allChildren;

    public function __construct($resource, $withChildren = false, $allChildren = false)
    {
        parent::__construct($resource);
        $this->withChildren = $withChildren;
        $this->allChildren = $allChildren;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $thumbnail = $this->attachment("thumbnail");
        if($this->withChildren) {
            $children = new CategoriesCollection($this->children()->get(), $this->allChildren);
        } else $children = null;

        return [
            "id" => $this->id,
            "name" => $this->name,
            "thumbnail" => $thumbnail ? $thumbnail->url : null,
            "description" => $this->description,
            "slug" => $this->slug,
            "defects" => $this->defects()->select("defects.id", "defects.name")->get(),
            "children" => $this->when($this->withChildren, $children)
        ];
    }
}
