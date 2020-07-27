<?php

namespace App\Http\Controllers;

use App\Http\Resources\DevicesCollection;
use App\Models\Category;
use App\Models\Device;
use App\Models\ProductGrid;
use Illuminate\Http\Request;
use App\Http\Requests\Device\CreateDevice as CreateDeviceRequest;
use App\Http\Resources\Device as DeviceResource;
use App\Http\Requests\Device\UpdateDevice as UpdateDeviceRequest;
use App\Models\Company;

class DevicesController extends Controller
{
    public function getDevices() {
        $devices = Device::orderBy("id", "desc")->paginate(10);
        $companies = Company::select("id", "name")->get();
        $categories = Category::whereIsLeaf()->select("id", "name")->get();
        $productsGrids = ProductGrid::select("id", "name", "type")->get();
        return response()->json([
            "devices" => (new DevicesCollection($devices))->response()->getData(true),
            "companies" => $companies,
            "categories" => $categories,
            "productsGrids" => $productsGrids
        ]);
    }

    public function createDevice(CreateDeviceRequest $request) {
        $company = Company::findOrFail($request->company);
        $device = $company->devices()->create($request->toArray());
        $device->categories()->attach($request->categories);
        if($request->has("use_products_grids") && $request->use_products_grids) {
            $device->productsGrids()->attach($request->products_grids);
        }
        $this->attachThumbnail($device, $request);
        return new DeviceResource($device);
    }

    public function updateDevice(Device $device, UpdateDeviceRequest $request) {
        $device->update($request->toArray());
        if($request->has("company")) {
            $company = Company::find($request->company);
            $device->company()->associate($company);
            $device->save();
        }
        if($request->has("detach_categories")) $device->categories()->detach($request->detach_categories);
        if($request->has("attach_categories")) $device->categories()->attach($request->attach_categories);
        if(!$request->has("use_products_grids")) $device->productsGrids()->detach();
        else {
            if($request->has("attach_products_grids")) $device->productsGrids()->attach($request->attach_products_grids);
            if($request->has("detach_products_grids")) $device->productsGrids()->detach($request->detach_products_grids);
        }
        $this->attachThumbnail($device, $request);
        return new DeviceResource($device);
    }

    public function removeDevice(Device $device, Request $request) {
        $device->forceDelete();
        $lastDeviceId = $request->lastDeviceId;
        $nextDevice = $lastDeviceId ? Device::orderBy("id", "desc")->where("id", "<", $lastDeviceId)->first() : null;
        $nextDevice = $nextDevice ? new DeviceResource($nextDevice) : null;
        return response()->json([ "device" => new DeviceResource($device), "nextDevice" => $nextDevice ]);
    }

    private function attachThumbnail(Device $device, $request) {
        if($request->hasFile("thumbnail")) {
            $device->attach($request->file("thumbnail"), [ "key" => "thumbnail" ]);
        }
    }
}
