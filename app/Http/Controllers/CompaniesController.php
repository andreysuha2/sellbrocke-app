<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Resources\CompaniesCollection;
use App\Http\Resources\Company as CompanyResource;
use App\Http\Requests\Company\StoreCompany as StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompany as UpdateCompanyRequest;

class CompaniesController extends Controller
{
    public function getCompanies() {
        $companies = Company::all();
        return new CompaniesCollection($companies);
    }

    public function getCompany(Company $company) {
        return new CompanyResource($company);
    }

    public function createCompany(StoreCompanyRequest $request) {
        $data = $request->toArray();
        $company = Company::create($data);
        $this->uploadLogo($request, $company);
        return new CompanyResource($company);
    }

    public function updateCompany(Company $company, UpdateCompanyRequest $request) {
        $data = $request->toArray();
        $company->update($data);
        $this->uploadLogo($request, $company);
        return new CompanyResource($company);
    }

    public function deleteCompany(Company $company) {
        // TODO: check relation with devices
        $company->delete();
        return new CompanyResource($company);
    }

    public function checkFreeSlug($slug) {
        $isFree = Company::where("slug", $slug)->exists();
        return response()->json([ "isFree" => (bool) $isFree ]);
    }

    private function uploadLogo($request, Company $company) {
        if($request->hasFile("logo")) {
            $company->attach($request->file("logo"), [ "key" => "logo" ]);
        }
    }
}
