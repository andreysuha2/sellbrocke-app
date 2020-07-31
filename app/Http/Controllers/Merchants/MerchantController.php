<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesPageCollection;
use App\Models\Category;
use App\Models\SearchSlug;
use Illuminate\Http\Request;
use App\Http\Resources\Merchant as MerchantResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SearchSlug as SearchSlugResource;

class MerchantController extends Controller
{
    public function index() {
        return new MerchantResource(Auth::guard('api-merchants')->user());
    }

    public function search($queryString = null, Request $request) {
        if(!$queryString) {
            $categories = Category::whereIsRoot()->get();
            $result = [
                "items" => new CategoriesPageCollection($categories),
                "type" => "categoriesList"
            ];
            return $result;
        } else {
            $searchSlug = SearchSlug::where("slug", $queryString)->firstOrFail();
            SearchSlugResource::withoutWrapping();
            return new SearchSlugResource($searchSlug);
        }
    }

}
