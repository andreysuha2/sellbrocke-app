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
}
