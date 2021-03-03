<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Resources\Customers\CustomersCollection;

class CustomersController extends Controller
{
    public $itemsPerPage;

    public function __construct(Request $request)
    {
        $this->itemsPerPage = env('DASHBOARD_ITEMS_PER_PAGE');
    }

    public function getCustomers()
    {
        $customers = Customer::paginate(10);
        return (new CustomersCollection($customers))->response()->getData(true);
    }

    public function search(Request $request)
    {
        if (empty($request->qs)) {
            return null;
        }

        $query = trim($request->qs);
        $separatorPos = stripos($query, ' ');

        if ($separatorPos > 0) {
            $firstPart = substr($query, 0, $separatorPos);
            $secondPart = substr($query, $separatorPos + 1);

            $customers = Customer::where('first_name', 'LIKE', "%{$firstPart}%")
                ->where('last_name', 'LIKE', "%{$secondPart}%")
                ->orWhere('first_name', 'LIKE', "%{$secondPart}%")
                ->where('last_name', 'LIKE', "%{$firstPart}%")
                ->paginate($this->itemsPerPage);

        } else {
            if (filter_var($query, FILTER_VALIDATE_EMAIL)) {
                $customers = Customer::where('email', $query)->paginate($this->itemsPerPage);
            } else {
                $customers = Customer::where('first_name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%")
                    ->paginate($this->itemsPerPage);
            }
        }

        return (new CustomersCollection($customers))->response()->getData(true);
    }
}
