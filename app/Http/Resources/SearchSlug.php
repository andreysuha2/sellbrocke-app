<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryPage as CategoryPageResource;
use App\Http\Resources\CompanyPage as CompanyPageResource;
use App\Http\Resources\DevicePage as DevicePageResource;

class SearchSlug extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "parts" => [
                "category" => $this->category_part,
                "company" => $this->company_part,
                "device" => $this->device_part
            ],
            "type" => $this->type,
            "item" => $this->getItem()
        ];
    }

    private function getItem() {
        $item = $this->search()->first();
        switch ($this->type) {
            case "category":
                return new CategoryPageResource($item);
            break;
            case "company":
                return new CompanyPageResource($item);
            break;
            case "device":
                return new DevicePageResource($item);
            break;
            default: return null;
        }
    }
}
