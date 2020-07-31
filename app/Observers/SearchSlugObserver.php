<?php

namespace App\Observers;

use App\Models\SearchSlug;

class SearchSlugObserver
{
    public function creating(SearchSlug $searchSlug) {
        $searchSlug->slug = $searchSlug->category_part;
        if($searchSlug->company_part) $searchSlug->slug .= "/$searchSlug->company_part";
        if($searchSlug->device_part) $searchSlug->slug .= "/$searchSlug->device_part";
    }

    public function updating(SearchSlug $searchSlug) {
        $searchSlug->slug = $searchSlug->category_part;
        if($searchSlug->company_part) $searchSlug->slug .= "/$searchSlug->company_part";
        if($searchSlug->device_part) $searchSlug->slug .= "/$searchSlug->device_part";
    }
}
