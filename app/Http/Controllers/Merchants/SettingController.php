<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use App\Http\Resources\Settings\SettingsCollection;

class SettingController extends Controller
{
    public function getGroup($group)
    {
        $allowedGroups = ['quotes', 'address'];

        if (!in_array($group, $allowedGroups)) {
            return null;
        }

        $settings = SettingService::getGroup($group);
        $settings = $settings->getData(false);

        return new SettingsCollection($settings);
    }
}