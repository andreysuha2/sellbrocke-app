<?php

namespace App\Http\Resources\Conditions;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Conditions\Condition as ConditionResource;

class ConditionCollection extends ResourceCollection
{
    public static $wrap = "conditions";
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($condition) {
            return new ConditionResource($condition);
        })->toArray();
    }
}
