<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryPage as CategoryPageResource;
use App\Http\Resources\CompanyPage as CompanyPageResource;
use App\Http\Resources\DevicePage as DevicePageResource;

class SearchSlug extends JsonResource
{
    private $request;

    public function __construct($resource, Request $request) {
        parent::__construct($resource);
        $this->request = $request;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $pageData = $this->getPageData();

        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "parts" => [
                "category" => $this->category_part,
                "company" => $this->company_part,
                "device" => $this->device_part
            ],
            "type" => $this->type,
            "item" => $pageData["item"],
            "list" => $this->when(isset($pageData["list"]), $pageData["list"] ?? null),
            "listType" => $this->when(isset($pageData["pageListType"]), $pageData["pageListType"] ?? null),
            "redirectTo" => $this->when(isset($pageData["redirectTo"]), $pageData["redirectTo"] ?? null)
         ];
    }

    private function getPageData() {
        $item = $this->search()->first();
        $result = [];
        switch ($this->type) {
            case "category":
                $result = $this->getCategoryData($item);
            break;
            case "company":
                $result = $this->getCompanyData($item);
            break;
            case "device":
                $result = $this->getDeviceData($item);
            break;
        }
        return $result;
    }

    private function getCategoryData($item) {
        $result = [];
        $result["item"] = new CategoryPageResource($item);
        if($item->isLeaf()) {
            $companies = $item->companies()->get();
            if($companies->count() > 1) {
                $result["list"] = new CompaniesPagesCollection($companies);
                $result["pageListType"] = "companies";
            } elseif($companies->count() === 1) {
                $result["pageListType"] = "devices";
                $result ["redirectTo"] = $this->slug . "/" . $companies->first()->slug;
            }
        } else {
            $result["pageListType"] = "categories";
            $result["list"] = new CategoriesPageCollection($item->children);
        }
        return $result;
    }

    private function getCompanyData($item) {
        $result = [];
        $searchCategory = SearchSlug::select("search_id")->where("slug", $this->category_part)->first();
        $result["item"] = new CompanyPageResource($item);
        $result["pageListType"] = "devices";
        $devices = $item->devices()->whereHas("categories", function ($query) use ($searchCategory) {
            $query->where("categories.id", $searchCategory->search_id);
        })->get();
        $result["list"] = new DevicesPageCollection($devices);
        return $result;
    }

    private function getDeviceData($item) {
        $result = [];
        if($item->use_products_grids && (!$this->request->has("carrier") || !$this->request->has("size"))) {
            $result["item"] = new DevicePageResource($item);
            $result["pageListType"] = "productsGrids";
            $sizes = $item->productsGrids->filter(function ($productGrid) { return $productGrid->type === "size"; });
            $carriers = $item->productsGrids->filter(function ($productGrid) { return $productGrid->type === "carrier"; });
            $result["list"] = [
                "sizes" => isset($sizes) && count($sizes) ? new ProductGridCollection($sizes) : null,
                "carriers" => isset($carriers) && count($carriers) ? new ProductGridCollection($carriers) : null
            ];
        } elseif($item) {
            $size = $item->use_products_grids ? $this->request->size : null;
            $carrier = $item->use_products_grids ? $this->request->carrier: null;
            $result["item"] = new DevicePageResource($item, $size, $carrier);
            $result["pageListType"] = "defects";
            $result["list"] = $item->defects()->flatten();
        }
        return $result;
    }
}