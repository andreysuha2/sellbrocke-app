<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    /**
     * Returns parameter's value by parameter's name
     *
     * @param string $paramName
     *
     * @return string|null
     */
    public static function getParameter($paramName)
    {
        if ($paramName) {
            $setting = Setting::where("key", "=", $paramName);
            if ($setting->first()) {
                return $setting->first()->value;
            }

            return null;
        }

        return null;
    }

    /**
     * Returns parameters collections by group name
     *
     * @param string $group
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    public static function getGroup($group)
    {
        if ($group) {
            return response()->json(Setting::where("group", "=", $group)->get());
        }

        return null;
    }

    public static function getParametersByGroup($group)
    {
        $paramsRaw = self::getGroup($group);
        $data = $paramsRaw->getData(false);
        $params = [];

        foreach ($data as $param) {
            $params[$param->key] = $param->value;
        }

        return $params;
    }

    /**
     * Stores parameters into the database
     *
     * @param array $data
     * @param string $group
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function store($data, $group)
    {
        if (!is_array($data) && empty($group)) {
            return response()->json([
                "status" => "error",
                "message" => "Your settings are not valid!"
            ]);
        }

        foreach ($data as $key => $value) {
            if (Setting::where("key", "=", $key)->first()) {
                Setting::where("key", "=", $key)->update(["value" => $value, "group" => $group]);
            } else {
                Setting::create(["key" => strtoupper($key), "value" => $value, "group" => $group]);
            }
        }

        return response()->json(["status" => "ok", "message" => "Your settings was saved successfully!"]);
    }
}