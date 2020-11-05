<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $devices = DB::select("SELECT `d`.`id`, `d`.`name`, `categories`, `cat_list`, `cat_id_1`, `cat_id_2`, `cat_id_3`, `cat_id_4`
            FROM `_devices` AS `d`
            LEFT JOIN `_categories` AS `c` ON `d`.`categories` = `c`.`cat_list`;");

        print_r($devices);
    }
}
