<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class ImportController extends Controller
{
    public function importCompanies()
    {
        $row = 1;
        if (($handle = fopen("companies_2.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                $params = '';
                $row++;

                /*                for ($c = 0; $c < $num; $c++) {
                                    if ($num - $c > 1) {
                                        $params .= "'" . $data[$c] . "', ";
                                    } else {
                                        $params .= "'" . $data[$c] . "'";
                                    }
                                }*/
                $company = new Company();
                $company->id = $data[0];
                $company->name = $data[1];
                $company->price_reduction = $data[2];
                $company->slug = $data[3];
                $company->created_at = $data[4];
                $company->updated_at = $data[5];
                $company->save();

//                $company->attach($request->file("logo"), [ "key" => "logo" ]);
                if (!empty($data[6])) {
                    $company->attach('images/company/' . $data[6], [ "key" => "logo" ]);
                }

            }
            fclose($handle);
        }

        return response()->json(['status' => 'ok']);
    }

    public function importDevices()
    {
        set_time_limit(120);
        $devices = DB::select("SELECT `d`.`id`,
                `d`.`name`,
                `d`.`slug`,
                `d`.`base_price`,
                `d`.`image_url`,
                `company_id`,
                `categories`,
                `cat_list`,
                `cat_id_1`,
                `cat_id_2`,
                `cat_id_3`,
                `cat_id_4`
            FROM `_devices` AS `d`
            LEFT JOIN `_categories` AS `c` ON `d`.`categories` = `c`.`cat_list`");

        foreach ($devices as $dev) {
            $image = null;
            if (!empty($dev->image_url)) {
                $parts = explode('|', $dev->image_url);
                $image = $parts[0];
            }


            $company = Company::findOrFail($dev->company_id);
            $device = $company->devices()->create([
                "name" => $dev->name,
                "slug" => $dev->slug,
                "base_price" => $dev->base_price,
                "use_products_grids" => 0,
                "image" => $image
            ]);

            $categories = [];

            if ($dev->cat_id_1 > 0) {
                $categories[] = $dev->cat_id_1;
            }

            if ($dev->cat_id_2 > 0) {
                $categories[] = $dev->cat_id_2;
            }

            if ($dev->cat_id_3 > 0) {
                $categories[] = $dev->cat_id_3;
            }

            if ($dev->cat_id_4 > 0) {
                $categories[] = $dev->cat_id_4;
            }

            if(count($categories) > 0) {
                $device->categories()->attach($categories);
            }

/*            if($request->has("use_products_grids") && $request->use_products_grids) {
                $device->productsGrids()->attach($request->products_grids);
            }*/
        }

        return response()->json(['status' => 'ok']);
    }

    public function importImages()
    {
        $counter = 0;
//        $devices = Device::all();
        $devices = Device::where('id', '>', 7085)->get();

        try {
            foreach ($devices as $device) {
                if (!empty($device->image)) {
                    $counter++;
                    if (file_exists($device->image)) {
                        $device->attach($device->image, [ "key" => "thumbnail" ]);
                    }
                }
                sleep(3);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'count' => $counter,
                'message' => $e->getMessage()
            ]);
        }

        return response()->json(['status' => 'ok', 'count' => $counter]);
    }
}
