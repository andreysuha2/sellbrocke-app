<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\Categories\CategoriesPageCollection;
use App\Http\Resources\Devices\DevicesPageCollection;
use App\Http\Resources\SearchSlug as SearchSlugResource;
use App\Models\Category;
use App\Models\Company;
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
            $query = trim($request->qs);

            $separatorPos = stripos($query, ' ');

            // If query string has two or more words
            if ($separatorPos > 0) {
                $firstPart = substr($query, 0, $separatorPos);
                $secondPart = substr($query, $separatorPos + 1);

                // Get companies which are matching to the first word
                $companies = Company::where('name', 'like', "%{$firstPart}%")->get();

                // If companies exist
                if (count($companies) > 0) {
                    // Get devices which are matching to the second part of the query
                    $devicesRaw = Device::where('name', 'like', "%{$secondPart}%")
                        ->whereIn('company_id', $companies)
                        ->paginate($this->itemsPerPage)
                        ->setPageName("pnum");
                } else {
                    // Else try to search companies which are matching to the second part of the query
                    $companies = Company::where('name', 'like', "%{$secondPart}%")->get();

                    // And if companies exist
                    if (count($companies) > 0) {
                        // Get devices which are matching to the first word in the search query
                        $devicesRaw = Device::where('name', 'like', "%{$firstPart}%")
                            ->whereIn('company_id', $companies)
                            ->paginate($this->itemsPerPage)
                            ->setPageName("pnum");
                    } else {
                        // Else try to get devices which are matching to the first or second word or whole query string
                        $devicesRaw = Device::where('name', 'like', "%{$firstPart}%")
                            ->orWhere('name', 'like', "%{$secondPart}%")
                            ->orWhere('name', 'like', "%{$query}%")
                            ->paginate($this->itemsPerPage)
                            ->setPageName("pnum");
                    }
                }
            } else {
                // Else if query string contains one word, get companies which are matching to this word
                $companies = Company::where('name', 'like', "%{$query}%")->get();

                // If companies exist
                if (count($companies) > 0) {
                    // Get all devices of these companies
                    $devicesRaw = Device::whereIn('company_id', $companies)
                        ->paginate($this->itemsPerPage)
                        ->setPageName("pnum");
                } else {
                    // Else get devices which are matching to the query string
                    $devicesRaw = Device::where('name', 'like', "%{$query}%")
                        ->paginate($this->itemsPerPage)
                        ->setPageName("pnum");
                }
            }

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
