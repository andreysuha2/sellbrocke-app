<?php

namespace App\Http\Controllers;

use App\Http\Resources\DefectsCollection;
use App\Models\Defect;
use Illuminate\Http\Request;
use App\Http\Resources\Defect as DefectResource;
use App\Http\Requests\Defect\StoreDefect as StoreDefectRequest;
use App\Http\Requests\Defect\UpdateDefect as UpdateDefectRequest;

class DefectsController extends Controller
{
    public function getDefects() {
        $defects = Defect::paginate(20);
        return (new DefectsCollection($defects))->response()->getData(true);
    }

    public function createDefect(StoreDefectRequest $request) {
        $data = $request->toArray();
        $defect = Defect::create($data);
        return new DefectResource($defect);
    }

    public function updateDefect(Defect $defect, UpdateDefectRequest $request) {
        $data = $request->toArray();
        $defect->update($data);
        return new DefectResource($defect);
    }

    public function getDefect(Defect $defect) {
        return new DefectResource($defect);
    }

    public function deleteDefect(Defect $defect) {
        $defect->delete();
        return new DefectResource($defect);
    }
}
