<?php

namespace App\Http\Resources;

use App\Http\Resources\Conditions\ConditionCollection;
use App\Http\Resources\Defects\DefectsCollection;
use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Categories\CategoryPage as CategoryPageResource;
use App\Http\Resources\Companies\CompanyPage as CompanyPageResource;
use App\Http\Resources\Devices\DevicePage as DevicePageResource;
use App\Http\Resources\Devices\DevicesPageCollection;
use App\Http\Resources\Companies\CompaniesPagesCollection;
use App\Http\Resources\Categories\CategoriesPageCollection;
use Illuminate\Pagination\Paginator;

class SearchSlug extends JsonResource
{
    private $request;
    private $pageParamName = "pnum";
    private $perPageCount = 50;

    public function __construct($resource, Request $request) {
        parent::__construct($resource);
        $this->request = $request;
        Paginator::currentPageResolver(function () use ($request) {
            return $request->has($this->pageParamName) ? $request[$this->pageParamName] : 1;
        });
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
            "paginator" => $this->when(isset($pageData["paginator"]), array_merge([
                "parameter_name" => $this->pageParamName
            ], $pageData["paginator"] ?? [])),
            "listType" => $this->when(isset($pageData["pageListType"]), $pageData["pageListType"] ?? null),
            "redirectTo" => $this->when(isset($pageData["redirectTo"]), $pageData["redirectTo"] ?? null),
            "conditions" => $this->when(isset($pageData["conditions"]), $pageData["conditions"] ?? null)
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
            $companies = $item
                ->companies()
                ->paginate($this->perPageCount)
                ->setPageName($this->pageParamName);

            if($companies->count() > 1) {
                $companiesPaginate = (new CompaniesPagesCollection($companies))->response()->getData(true);
                $result["list"] = $companiesPaginate["data"];
                $result["paginator"] = $companiesPaginate["meta"];
                $result["pageListType"] = "companies";
            } elseif($companies->count() === 1) {
                $result["pageListType"] = "devices";
                $result ["redirectTo"] = $this->slug . "/" . $companies->first()->slug;
            }
        } else {
            $result["pageListType"] = "categories";
            $categories = (new CategoriesPageCollection(
                $item->children()
                    ->paginate($this->perPageCount)
                    ->setPageName($this->pageParamName)
            ))->response()->getData(true);
            if (count($categories['data']) == 1) {
                $result ["redirectTo"] = $categories['data'][0]['slug'];
            }
            $result["list"] = $categories["data"];
            $result["paginator"] = $categories["meta"];
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
        })->orderBy('id', 'desc')
            ->paginate($this->perPageCount)
            ->setPageName($this->pageParamName);
        $devicesData = (new DevicesPageCollection($devices))->response()->getData(true);
        $result["list"] = $devicesData["data"];
        $result["paginator"] = $devicesData["meta"];
        return $result;
    }

    private function getDeviceData($item) {
        $result = [];

        if ($item) {
            $size = $item->use_products_grids ? $this->request->size : null;
            $carrier = $item->use_products_grids ? $this->request->carrier: null;
            $result["item"] = new DevicePageResource($item, $size, $carrier);
            $result["pageListType"] = "defects";
            $result["list"] = new DefectsCollection($item->defects()->get());
            $result["conditions"] = new ConditionCollection(Condition::all());
        }

        return $result;
    }
}
