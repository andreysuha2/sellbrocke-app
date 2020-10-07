<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Resources\Orders\OrdersCollection;
use App\Http\Resources\Orders\OrdersPageCollection;
use App\Http\Resources\Orders\Order as OrderResource;
use App\Http\Requests\Order\CreateOrder as CreateOrderRequest;
use App\Jobs\OrderCreateNotificationJob;
use App\Models\Condition;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Order;
use App\Models\OrderDevice;
use App\Models\Shipment;
use App\Services\FedExService;
use App\Services\UPSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class OrdersController extends Controller
{
    public function getOrders(Customer $customer) {
        $orders = $customer->orders()->paginate(10);
        return (new OrdersPageCollection($orders))->response()->getData(true);
    }

    public function getOrder(Customer $customer, Order $order) {
        Gate::forUser(Auth::guard("api-merchants")->user())->authorize("get-order", [ $customer, $order ]);
        return new OrderResource($order);
    }

    public function createOrder(Customer $customer, CreateOrderRequest $request) {
        Gate::forUser(Auth::guard("api-merchants")->user())->authorize("create-order", $customer);
        $order = $customer->orders()->create(array_merge(
            ["status" => "pending", "confirmation_key" => Str::random(32)],
            $request->toArray()
        ));
        $shippingData = $this->createShipment($request);
        $shipping = $order->shipment()->create($shippingData);
        $shipping->storeLabel($shippingData["label"]);
        collect($request->devices)->each(function ($deviceData) use ($order) {
            $orderDevice = new OrderDevice();
            $device = Device::find($deviceData["id"]);
            $condition = Condition::find($deviceData["condition"]);
            $orderDevice->order()->associate($order);
            $orderDevice->device()->associate($device);
            $orderDevice->condition()->associate($condition);
            $orderDevice->save();
            if(isset($deviceData["defects"])) $orderDevice->defects()->attach($deviceData["defects"]);
            if($device->use_products_grids && isset($deviceData["productsGrids"])) {
                $orderDevice->products_grids()->attach($deviceData["productsGrids"]);
            }
        });

        dispatch(new OrderCreateNotificationJob($order, $customer));

        return new OrderResource($order);
    }

    public function updateOrderStatus(Request $request, $customer) {
        $action = null;

        if (empty($request->id) && empty($request->key)) {
            return response()->json([
                'action' => $action,
                'message' => 'Incorrect order data!'
            ], 400);
        }

        $order = Order::where('id', $request->id)
            ->where('customer_id', $customer->id)
            ->where('confirmation_key', $request->key)
            ->first();

        if (is_null($order)) {
            return response()->json([
                'action' => $action,
                'message' => 'An order was not found!'
            ], 404);
        }

        if ($request->action == 'confirm') {
            $order->status = 'open';
            $order->confirmation_key = null;
            $action = 'confirmed';
        } else if ($request->action == 'cancel') {
            $order->status = 'cancelled';
            $order->confirmation_key = null;
            $action = 'cancelled';
        }

        $order->save();

        return response()->json([
            'action' => $action,
            'message' => "Your order was successfully {$action}!"
        ], 200);
    }

    private function createShipment(Request $request) {
        if($request->shipment["type"] === "UPS") {
            $shipping = new UPSService(new Client());
            $query = [
                "ShipmentRequest" => [
                    "Shipment" => [
                        "Description" => $request->shipment["description"] ?? "test",
                        "Shipper" => [
                            "Name" => "Sellbroke",
                            "AttentionName" => "Sellbroke",
                            "Phone" => [
                                "Number" => "1234567890"
                            ],
                            "ShipperNumber" => env("UPS_SHIPPER_NUMBER"),
                            "Address" => [
                                "AddressLine" => "Saks Fifth Avenue‎",
                                "City" => "New York",
                                "StateProvinceCode" => "NY",
                                "PostalCode" => "10001",
                                "CountryCode" => "US"
                            ]
                        ],
                        "ShipTo" => [
                            "Name" => "ShipToName",
                            "AttentionName" => "AttentionName",
                            "Phone" => [
                                "Number" => "1234567890"
                            ],
                            "FaxNumber" => "1234567999",
                            "Address" => [
                                "AddressLine" => "Sixth Avenue",
                                "City" => "New York",
                                "StateProvinceCode" => "NY",
                                "PostalCode" => "10001",
                                "CountryCode" => "US"
                            ]
                        ],
                        "ShipFrom" => $request->shipment["shipFrom"],
                        "PaymentInformation" => [
                            "ShipmentCharge" => [
                                "Type" => "01",
                                "BillShipper" => [
                                    "AccountNumber" => env("UPS_SHIPPER_NUMBER")
                                ]
                            ]
                        ],
                        "Service" => $request->shipment["service"],
                        "Package" => [ $request->shipment["package"] ],
                    ],
                    "LabelSpecification" => [
                        "LabelImageFormat" => [
                            "Code" => "GIF"
                        ]
                    ]
                ]
            ];
            $shippingResponse = $shipping->shipment($query)["ShipmentResponse"];
            $shippingLabel = $shippingResponse["ShipmentResults"]["PackageResults"]["ShippingLabel"]["GraphicImage"];
            $shippingData = [
                "type" => "UPS",
                "tracking_number" => $shippingResponse["ShipmentResults"]["ShipmentIdentificationNumber"],
                "weight" => $shippingResponse["ShipmentResults"]["BillingWeight"]["Weight"],
                "weight_code" => $shippingResponse["ShipmentResults"]["BillingWeight"]["UnitOfMeasurement"]["Code"],
                "total_charges" => $shippingResponse["ShipmentResults"]["ShipmentCharges"]["TotalCharges"]["MonetaryValue"],
                "currency_code" => $shippingResponse["ShipmentResults"]["ShipmentCharges"]["TotalCharges"]["CurrencyCode"],
                "status" => "created",
                "label" => $shippingLabel
            ];
        } else if($request->shipment["type"] === "FEDEX") {
            $shipping = new FedExService();
            $query = [
                'version' => [
                    'major' => 23,
                    'intermediate' => 0,
                    'minor' => 0,
                    'service_id' => 'ship',
                ],
                'shipperAddress' => $request->shipment["shipperAddress"],
                'shipperContact' => $request->shipment["shipperContact"],
                'recipientAddress' => [
                    'line1' => 'Address Line 1',
                    'city' => 'Herndon',
                    'state_code' => 'VA',
                    'postal_code' => '20171',
                    'country_code' => 'US'
                ],
                'recipientContact' => [
                    'person_name' => 'Person Name',
                    'phone' => '1234567890',
                ],
                'package' => [
                    'weight' => [
                        'value' => $request->shipment["weight"],
                        'units' => 'LB'
                    ]
                ]
            ];
            $shippingResponse = $shipping->shipment($query);
            $shippingResponseDetails = $shippingResponse["CompletedShipmentDetail"]["CompletedPackageDetails"][0]["PackageRating"]["PackageRateDetails"][0];
            $shippingData = [
                "type" => "FEDEX",
                "tracking_number" => $shippingResponse["CompletedShipmentDetail"]["MasterTrackingId"]["TrackingNumber"],
                "weight" => $shippingResponseDetails["BillingWeight"]["Value"],
                "weight_code" => $shippingResponseDetails["BillingWeight"]["Units"],
                "total_charges" => $shippingResponseDetails["NetCharge"]["Amount"],
                "currency_code" => $shippingResponseDetails["NetCharge"]["Currency"],
                "status" => "created",
                "label" => $shippingResponse["CompletedShipmentDetail"]["CompletedPackageDetails"][0]["Label"]["Parts"][0]["Image"]
            ];
        } else {
            return abort("404");
        }

        return $shippingData;
    }

    public function setShipmentStatus(Request $request)
    {
        $shipment = Shipment::find($request->id);
        $shipment->status = 'label-printed';
        $shipment->save();

        return response()->json(["success" => true, "status" => "label-printed"]);
    }

    public function getOrdersByCustomerId($merchantCustomerId)
    {
        $customer = Customer::where('merchant_customer_id', $merchantCustomerId)->first();
        $orders = $customer->orders;

        return new OrdersCollection($orders);
    }
}
