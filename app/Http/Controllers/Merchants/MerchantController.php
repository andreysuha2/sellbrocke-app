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
use App\Models\SerialNumber;
use App\Services\SettingService as Config;
use Illuminate\Http\Request;
use App\Http\Resources\Merchants\Merchant as MerchantResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    public $pageParamName = "pnum";
    public $itemsPerPage = 50;

    public function __construct(Request $request)
    {
        Paginator::currentPageResolver(function () use ($request) {
            return $request->has($this->pageParamName) ? $request[$this->pageParamName] : 1;
        });
    }

    public function index()
    {
        return new MerchantResource(Auth::guard('api-merchants')->user());
    }

    public function search(Request $request, $queryString = null)
    {
        if ($request->has('qs')) {
            $query = trim($request->get('qs'));
            $searchParts = explode(' ', $query);
            $searchPartsCount = count($searchParts);

            // If query string has two or more words
            if ($searchPartsCount > 1) {
                $searchPhrase = implode("%", $searchParts);
                $devicesRaw = Device::select(
                    "devices.id",
                    "devices.name",
                    "devices.slug",
                    "companies.name AS company_name",
                    "companies.slug AS company_slug",
                    "categories.slug AS category_slug",
                    "devices.base_price",
                    "companies.price_reduction",
                    "devices.use_products_grids",
                    "devices.created_at"
                )->where(DB::raw("CONCAT(`companies`.`name`, ' ', `devices`.`name`)"), "LIKE", "%{$searchPhrase}%")
                    ->join('companies', 'devices.company_id', '=', 'companies.id')
                    ->join('category_device', 'category_device.device_id', '=', 'devices.id')
                    ->join('categories', 'category_device.category_id', '=', 'categories.id')
                    ->orderBy('devices.id', 'desc')
                    ->paginate($this->itemsPerPage)
                    ->setPageName($this->pageParamName);
            } else {
                // Else if query string contains one word, get companies which are matching to this word
                $companies = Company::where('name', 'like', "%{$query}%")->get();

                $devicesRaw = Device::where('name', 'like', "%{$query}%")
                    ->orWhere(function ($query) use ($companies) {
                        $query->whereIn('company_id', $companies);
                    })
                    ->orderBy('id', 'desc')
                    ->paginate($this->itemsPerPage)
                    ->setPageName($this->pageParamName);
            }

            $devices = (new DevicesPageCollection($devicesRaw))->response()->getData(true);

            $resultSet = [
                'list' => $devices['data'],
                'listType' => 'devices',
                "paginator" => array_merge(
                    $devices["meta"],
                    ["parameter_name" => $this->pageParamName],
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
            $searchSlug = SearchSlug::where("slug", $queryString)
                ->orWhere('device_part', $queryString)
                ->firstOrFail();
            SearchSlugResource::withoutWrapping();

            return new SearchSlugResource($searchSlug, $request);
        }
    }

    public function searchBySerialNumber($serialNumber)
    {
        if (empty($serialNumber)) {
            return false;
        }

        $devices = [];
        $sn = null;
        $serialNumber = SerialNumber::where('number', $serialNumber)->first();

        if (!is_null($serialNumber)) {
            $sn = $serialNumber->number;
            $salesNumbers = $serialNumber->salesNumbers;

            $devicesRaw = Device::where(function ($query) use ($salesNumbers) {
                for ($i = 0; $i < count($salesNumbers); $i++) {
                    $query->orWhere('name', 'like', '%' . $salesNumbers[$i]->number . '%');
                }
            })->orderBy('id', 'desc')
            ->paginate($this->itemsPerPage)
            ->setPageName($this->pageParamName);

            $devices = (new DevicesPageCollection($devicesRaw))->response()->getData(true);
        }

        $resultSet = [
            'parts' => [
                'category' => 'apple-devices',
                'company' => 'apple',
                'devices' => null
            ],
            'list' => !empty($devices['data']) ? $devices['data'] : [],
            'listType' => 'devices',
            "paginator" => array_merge(
                !empty($devices["meta"]) ? $devices['meta'] : [],
                ["parameter_name" => $this->pageParamName],
                ["query" => "serial_number={$sn}"]
            )
        ];
        return response()->json($resultSet);
    }

    public function getPackageSettings()
    {
        $fedExPackageWeight = Config::getParameter("FEDEX_PACKAGE_WEIGHT");
        $upsPackageWeight = Config::getParameter("UPS_PACKAGE_WEIGHT");

        return response()->json([
            'fedExPackageWeight' => !is_null($fedExPackageWeight) ? $fedExPackageWeight : 5,
            'upsPackageWeight' => !is_null($upsPackageWeight) ? $upsPackageWeight : 5
        ]);
    }
}
