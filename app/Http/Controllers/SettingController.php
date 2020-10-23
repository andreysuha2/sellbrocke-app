<?php

namespace App\Http\Controllers;

use App\Http\Resources\Settings\SettingsCollection;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getGroup($group)
    {
        $settings = SettingService::getGroup($group);

        $settings = $settings->getData(false);

        return new SettingsCollection($settings);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        unset($data['group']);
        $group = $request->group;

        return SettingService::store($data, $group);
    }
}
