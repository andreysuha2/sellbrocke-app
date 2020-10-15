<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\Categories\CategoriesPageCollection;
use App\Http\Resources\Devices\DevicesPageCollection;
use App\Http\Resources\SearchSlug as SearchSlugResource;
use App\Models\Category;
use App\Models\Device;
use App\Models\SearchSlug;
use Illuminate\Http\Request;
use App\Http\Resources\Merchants\Merchant as MerchantResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;

class MerchantController extends Controller
{
    public $pageParamName = "pnum";
    public $itemsPerPage = 50;

    public function __construct(Request $request) {
        Paginator::currentPageResolver(function () use ($request) {
            return $request->has($this->pageParamName) ? $request[$this->pageParamName] : 1;
        });
    }

    public function index() {
        return new MerchantResource(Auth::guard('api-merchants')->user());
    }

    public function search($queryString = null, Request $request) {
        if ($request->has('qs')) {
            $query = $request->qs;

            $devicesRaw = Device::where('name', 'like', "%{$query}%")->paginate($this->itemsPerPage)->setPageName("pnum");
            $devices = (new DevicesPageCollection($devicesRaw))->response()->getData(true);

            $resultSet = [
                'list' => $devices['data'],
                'listType' => 'devices',
                "paginator" => array_merge(
                    $devices["meta"],
                    ["parameter_name" => "pnum"],
                    ["query" => "qs={$query}"]
                )
            ];
            return response()->json($resultSet);

        } else if (!$queryString) {
            $categories = Category::whereIsRoot()->get();
            $result = [
                "list" => new CategoriesPageCollection($categories),
                "listType" => "categoriesList"
            ];
            return response()->json($result);

        } else {
            $searchSlug = SearchSlug::where("slug", $queryString)->firstOrFail();
            SearchSlugResource::withoutWrapping();
            return new SearchSlugResource($searchSlug, $request);
        }
    }

}
