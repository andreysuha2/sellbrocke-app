<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryPage as CategoryPageResource;
use App\Http\Resources\CategoriesCollection;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\Merchant as MerchantResource;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    public function index() {
        return new MerchantResource(Auth::guard('api-merchants')->user());
    }

    public function search($queryString = null, Request $request) {
        if(!$queryString) {
            $categories = Category::whereIsRoot()->get();
            $result = [
                "items" => new CategoriesCollection($categories),
                "type" => "categoriesList"
            ];
        } elseif(Category::where("slug", $queryString)->exists()) {
            $category = Category::where("slug", $queryString)->first();
            $result = [
                "category" => new CategoryPageResource($category),
                "type" => "categoryPage"
            ];
        } else { $result = "Not found"; }
        return $result;
    }
}
