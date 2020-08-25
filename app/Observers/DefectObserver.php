<?php

namespace App\Observers;
use App\Models\Defect;

class DefectObserver
{
    public function deleted(Defect $defect) {
        $defect->categories()->detach();
    }
}
