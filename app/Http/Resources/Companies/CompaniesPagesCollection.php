<?php

namespace App\Http\Resources\Companies;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Companies\CompanyPage as CompanyPageResource;

class CompaniesPagesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($companyPage) {
            return new CompanyPageResource($companyPage);
        })->toArray();
    }
}
