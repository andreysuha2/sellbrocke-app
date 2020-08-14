<?php

namespace App\Http\Resources\Defects;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Defects\Defect as DefectResource;

class DefectsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = "defects";

    public function toArray($request)
    {
        return $this->collection->map(function ($defect) {
            return new DefectResource($defect);
        })->toArray();
    }
}
