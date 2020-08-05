<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\SearchSlug;

class CompanyObserver
{
    public function deleted(Company $company) {
        $company->searchSlugs()->delete();
    }

    public function updated(Company $company) {
        if($company->slug !== $company->getOriginal("slug")) {
            $oldSlug = $company->getOriginal("slug");
            $searchSlugs = SearchSlug::where("company_part", $oldSlug)->get();
            $searchSlugs->each(function ($searchSlug) use ($company) {
                $searchSlug->company_part = $company->slug;
                $searchSlug->save();
            });
        }
    }
}
