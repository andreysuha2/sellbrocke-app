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
                $companiesFullList = [];
                $partsToRemove = [];

                // Loop through all parts of the query string and try to get companies
                // which are matching to the one of the query string parts
                for ($i = 0; $i < count($searchParts); $i++) {
                    $companies = Company::where('name', 'like', "%{$searchParts[$i]}%")->get();

                    if (count($companies) > 0) {
                        if (empty($companiesFullList)) {
                            for ($k = 0; $k < count($companies); $k++) {
                                if (!in_array($companies[$k]->id, $companiesFullList)) {
                                    $companiesFullList[] = $companies[$k]->id;
                                }
                            }
                        }
                        $partsToRemove[] = $i;
                    }

                }

                for ($a = 0; $a < count($partsToRemove); $a++) {
                    unset($searchParts[$partsToRemove[$a]]);
                }

                sort($searchParts);

                // If companies exist
                if (count($companiesFullList) > 0) {
                    $firstPart = array_shift($searchParts);
                    if (count($searchParts) === 0) {
                        // Get devices which are matching to the first part of the rest query string
                        $devicesRaw = Device::where('name', 'like', "%{$firstPart}%")
                            ->whereIn('company_id', $companiesFullList)
                            ->orderBy('id', 'desc')
                            ->paginate($this->itemsPerPage)
                            ->setPageName($this->pageParamName);
                    } else {
                        // Else get devices which are matching to the first part of the rest query string
                        $intermediateResults = Device::where('name', 'like', "%{$firstPart}%")
                            ->whereIn('company_id', $companiesFullList)
                            ->get();

                        // and search devices which are matching to the other parts of the query string
                        // among found devices
                        for ($i = 0; $i < count($searchParts); $i++) {
                            $ids = [];
                            for ($j = 0; $j < count($intermediateResults); $j++) {
                                $ids[] = $intermediateResults[$j]->id;
                            }

                            if ($i <> count($searchParts) - 1) {
                                $intermediateResults = Device::where('name', 'like', "%{$searchParts[$i]}%")
                                    ->whereIn('id', $ids)
                                    ->get();
                            } else {
                                $devicesRaw = Device::where('name', 'like', "%{$searchParts[$i]}%")
                                    ->whereIn('id', $ids)
                                    ->orderBy('id', 'desc')
                                    ->paginate($this->itemsPerPage)
                                    ->setPageName($this->pageParamName);
                            }
                        }
                    }

                } else {
                    $firstPart = array_shift($searchParts);

                    $intermediateResults = Device::where('name', 'like', "%{$firstPart}%")
                        ->get();

                    for ($i = 0; $i < count($searchParts); $i++) {
                        $ids = [];
                        for ($j = 0; $j < count($intermediateResults); $j++) {
                            $ids[] = $intermediateResults[$j]->id;
                        }

                        if ($i <> count($searchParts) - 1) {
                            $intermediateResults = Device::where('name', 'like', "%{$searchParts[$i]}%")
                                ->whereIn('id', $ids)
                                ->get();
                        } else {
                            $devicesRaw = Device::where('name', 'like', "%{$searchParts[$i]}%")
                                ->whereIn('id', $ids)
                                ->orderBy('id', 'desc')
                                ->paginate($this->itemsPerPage)
                                ->setPageName($this->pageParamName);
                        }
                    }
                }
            } else {
                // Else if query string contains one word, get companies which are matching to this word
                $companies = Company::where('name', 'like', "%{$query}%")->get();

                // If companies exist
                if (count($companies) > 0) {
                    // Get all devices of these companies
                    $devicesRaw = Device::whereIn('company_id', $companies)
                        ->orderBy('id', 'desc')
                        ->paginate($this->itemsPerPage)
                        ->setPageName($this->pageParamName);
                } else {
                    // Else get devices which are matching to the query string
                    $devicesRaw = Device::where('name', 'like', "%{$query}%")
                        ->orderBy('id', 'desc')
                        ->paginate($this->itemsPerPage)
                        ->setPageName($this->pageParamName);
                }
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
