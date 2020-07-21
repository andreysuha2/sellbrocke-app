<?php

namespace App\Http\Controllers;

use App\Http\Resources\DevicesCollection;
use App\Models\Category;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Requests\Device\CreateDevice as CreateDeviceRequest;
use App\Http\Resources\Device as DeviceResource;
use App\Http\Requests\Device\UpdateDevice as UpdateDeviceRequest;
use App\Models\Company;

class DevicesController extends Controller
{
    public function getDevices() {
        $devices = Device::paginate(5);
        $companies = Company::select("id", "name")->get();
        $categories = Category::whereIsLeaf()->select("id", "name")->get();
        return response()->json([
            "devices" => (new DevicesCollection($devices))->response()->getData(true),
            "companies" => $companies,
            "categories" => $categories
        ]);
    }

    public function createDevice(CreateDeviceRequest $request) {
        $company = Company::findOrFail($request->company);
        $device = $company->devices()->create($request->toArray());
        $device->categories()->attach($request->categories);
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
        $this->attachThumbnail($device, $request);
        return new DeviceResource($device);
    }

    public function removeDevice(Device $device) {
        $device->forceDelete();
        return new DeviceResource($device);
    }

    private function attachThumbnail(Device $device, $request) {
        if($request->hasFile("thumbnail")) {
            $device->attach($request->file("thumbnail"), [ "key" => "thumbnail" ]);
        }
    }
}
