<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Resources\Customers\CustomersCollection;

class CustomersController extends Controller
{
    public function getCustomers() {
        $customers = Customer::paginate(10);
        return (new CustomersCollection($customers))->response()->getData(true);
    }

    public function search(Request $request) {
        if (empty($request->qs)) {
            return null;
        }

        $query = trim($request->qs);
        $separatorPos = stripos($query, ' ');
        $customers = null;

        if ($separatorPos > 0) {
            $firstPart = substr($query, 0, $separatorPos);
            $secondPart = substr($query, $separatorPos + 1);

            $customers = Customer::where('first_name', 'LIKE', "%{$firstPart}%")
                ->where('last_name', 'LIKE', "%{$secondPart}%")
                ->orWhere('first_name', 'LIKE', "%{$secondPart}%")
                ->where('last_name', 'LIKE', "%{$firstPart}%")
                ->get();

        } else {
            if (filter_var($query, FILTER_VALIDATE_EMAIL)) {
                $customers = Customer::where('email', 'LIKE', "%{$query}%")->get();
            } else {
                $customers = Customer::where('first_name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%")
                    ->get();
            }
        }

        return $customers;
    }
}
