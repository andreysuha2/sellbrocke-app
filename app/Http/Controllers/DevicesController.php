<?php

namespace App\Http\Controllers;

use App\Http\Resources\DevicesCollection;
use App\Models\Category;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Requests\Device\CreateDevice as CreateDeviceRequest;
use App\Http\Resources\Device as DeviceResource;
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
        return new DeviceResource($device);
    }

    private function attachThumbnail(Device $device, $request) {
        if($request->hasFile("thumbnail")) {
            $device->attach($request->file("thumbnail"), [ "key" => "thumbnail" ]);
        }
    }
}
