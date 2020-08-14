<?php

namespace App\Http\Resources\Companies;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Companies\Company as CompanyResource;

class CompaniesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static $wrap = "companies";

    public function toArray($request)
    {
        return $this->collection->map(function ($company) {
            return new CompanyResource($company);
        });
    }
}
