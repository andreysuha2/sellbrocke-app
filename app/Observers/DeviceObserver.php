<?php

namespace App\Observers;
use App\Models\Device;

class DeviceObserver
{
    public function forceDeleted(Device $device) {
        $device->categories()->detach();
        $device->productsGrids()->detach();
    }
}
