<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryPage as CategoryPageResource;
use App\Http\Resources\CategoriesCollection;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\Merchant as MerchantResource;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    public function index() {
        return new MerchantResource(Auth::guard('api-merchants')->user());
    }

    public function search($queryString = null, Request $request) {
//        if(!$queryString) {
//            $categories = Category::whereIsRoot()->get();
//            $result = [
//                "items" => new CategoriesCollection($categories),
//                "type" => "categoriesList"
//            ];
//        } elseif(Category::where("slug", $queryString)->exists()) {
//            $category = Category::where("slug", $queryString)->first();
//            $result = [
//                "category" => new CategoryPageResource($category),
//                "type" => "categoryPage"
//            ];
//        }
        $queryInfo = $this->getQueryInfo($queryString);
        return $queryInfo;
    }

    private function getQueryInfo($queryString) {
        $queryList = explode("/", $queryString);
        $queryString1 = implode("/", array_slice($queryList, 0, -1));
        $queryString2 = implode("/", array_splice($queryList, 0, -2));
        $categories = Category::where("slug", $queryString)
                ->orWhere("slug", $queryString1)
                ->orWhere("slug", $queryString2)
                ->select("slug")->get();
        if($categories->isEmpty()) $result = [ "type" => null, "slug" => $queryString ];
        else {
            $categorySlug = $categories->reduce(function ($slug, $item) {
                return mb_strlen($slug) < mb_strlen($item->slug) ? $item->slug : $slug;
            }, "");
            if($categorySlug === $queryString) $result = [ "type" => "category", "slug" => $queryString ];
            else {
                $queryList = explode("/", mb_substr($queryString, mb_strlen($categorySlug) + 1));
                if(count($queryList) < 2 || !Company::where("slug", $queryList)->exists()) $result = [ "type" => null, "slug" => $queryString ];
                elseif(count($queryList) === 1) $result = [ "type" => "company", "slug" => $queryList[0] ];
                else $result = null;
            }
        }
        return $result;
    }
}
