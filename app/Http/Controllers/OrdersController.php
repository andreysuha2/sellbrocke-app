<?php

namespace App\Http\Controllers;

use App\Jobs\OrderReminderNotificationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Http\Resources\Orders\Order as OrderResource;
use App\Http\Resources\Orders\OrdersCollection;
use App\Http\Requests\Order\UpdateStatus as OrderUpdateStatusRequest;
use App\Services\FedExService;
use App\Services\UPSService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public $fedExService;
    public $itemsPerPage;

    public function __construct(Request $request)
    {
        $this->itemsPerPage = env('DASHBOARD_ITEMS_PER_PAGE');
    }

    public function getOrders(Request $request)
    {
        if(empty($request->qs)) {
            $orders = Order::orderBy("id", "DESC")->paginate(10);
            return (new OrdersCollection($orders))->response()->getData(true);
        } else {
            return $this->search($request);
        }
    }

    public function getOrder(Order $order)
    {
        if ($order->shipment->type == 'FEDEX') {
            $this->fedExService = new FedExService();
            $tracking = $this->fedExService->tracking($order->shipment->tracking_number);
        } else {
            $shipping = new UPSService(new Client());
            $tracking = $shipping->tracking($order->shipment->tracking_number);
        }

        return new OrderResource($order, $tracking);
    }

    public function updateOrderStatus(Order $order, OrderUpdateStatusRequest $request)
    {
        $order->status = $request->status;
        $order->save();
        return new OrderResource($order);
    }

    public function orderReminder(Order $order)
    {
        dispatch(new OrderReminderNotificationJob($order));
    }

    public function search(Request $request)
    {
        $query = trim($request->qs);
        $separatorPos = stripos($query, ' ');
        $orders = [];

        if (is_numeric($query)) {
            $orders = Order::where('id', $query)->paginate($this->itemsPerPage);
        } else {
            if ($separatorPos > 0) {
                $firstPart = substr($query, 0, $separatorPos);
                $secondPart = substr($query, $separatorPos + 1);

                $customers = Customer::select('id')
                    ->where('first_name', 'LIKE', "%{$firstPart}%")
                    ->where('last_name', 'LIKE', "%{$secondPart}%")
                    ->orWhere('first_name', 'LIKE', "%{$secondPart}%")
                    ->where('last_name', 'LIKE', "%{$firstPart}%")
                    ->get();
            } else {
                if (filter_var($query, FILTER_VALIDATE_EMAIL)) {
                    $customers = Customer::where('email', $query)->paginate($this->itemsPerPage);
                } else {
                    $customers = Customer::select('id')
                        ->where('first_name', 'LIKE', "%{$query}%")
                        ->orWhere('last_name', 'LIKE', "%{$query}%")
                        ->get();
                }
            }

            if (count($customers) > 0) {
                $orders = Order::whereIn('customer_id', $customers)->paginate($this->itemsPerPage);
            }
        }

        return (new OrdersCollection($orders))->response()->getData(true);
    }
}
