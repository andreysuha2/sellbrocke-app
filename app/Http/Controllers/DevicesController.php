<?php

namespace App\Http\Controllers;

use App\Http\Resources\Company;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Requests\Device\CreateDevice as CreateDeviceRequest;
use App\Http\Resources\Device as DeviceResource;

class DevicesController extends Controller
{
    public function getDevices() {

    }

    public function createDevice(CreateDeviceRequest $request) {
        $company = Company::findOrFail($request->company_id);
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
