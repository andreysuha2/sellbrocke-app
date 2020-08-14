<?php

namespace App\Http\Controllers;

use App\Http\Resources\Defects\DefectsCollection;
use App\Models\Defect;
use Illuminate\Http\Request;
use App\Http\Resources\Defects\Defect as DefectResource;
use App\Http\Requests\Defect\StoreDefect as StoreDefectRequest;
use App\Http\Requests\Defect\UpdateDefect as UpdateDefectRequest;

class DefectsController extends Controller
{
    public function getDefects() {
        $defects = Defect::orderBy("id", "desc")->paginate(10);
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

    public function deleteDefect(Defect $defect, Request $request) {
        $defect->delete();
        $lastDefectId = $request->lastDefectId;
        $nextDefect = $lastDefectId ? Defect::orderBy("id", "desc")->where("id", "<", $lastDefectId)->first() : null;
        $nextDefect = $nextDefect ? new DefectResource($nextDefect) : null;
        return response()->json([ "defect" => new DefectResource($defect), "nextDefect" => $nextDefect ]);
    }
}
