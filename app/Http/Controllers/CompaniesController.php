<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\CompaniesCollection;
use App\Http\Resources\Company as CompanyResource;

class CompaniesController extends Controller
{
    public function getAll() {
        $companies = Company::paginate(20);
        return (new CompaniesCollection($companies))->response()->getData(true);
    }

    public function getCompany(Company $company) {
        return new CompanyResource($company);
    }

    public function createCompany(Request $request) {
        $data = $request->toArray();
        // TODO: validate $data
        $company = Company::create($data);
        return new CompanyResource($company);
    }

    public function updateCompany(Company $company, Request $request) {
        $data = $request->toArray();
        // TODO: validate $data
        $company->update($data);
        return new CompanyResource($company);
    }

    public function deleteCompany(Company $company) {
        $company->delete();
        return new CompanyResource($company);
    }

    public function checkFreeSlug($slug) {
        $isFree = Company::where("slug", $slug)->exists();
        return response()->json([ "isFree" => (bool) $isFree ]);
    }
}
