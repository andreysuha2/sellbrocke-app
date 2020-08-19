<?php

namespace App\Http\Controllers;

use App\Services\FedExService;

class FedExController extends Controller
{
    public $fedEx;

    public function __construct(FedExService $fedEx)
    {
        $this->fedEx = $fedEx;
    }

    public function shipmentRequest()
    {
        $shipmentDetails = [
            'version' => [
                'major' => 23,
                'intermediate' => 0,
                'minor' => 0,
                'service_id' => 'ship',
            ],
            'shipperAddress' => [
                'line1' => 'Address Line 1',
                'city' => 'Austin',
                'state_code' => 'TX',
                'postal_code' => '73301',
                'country_code' => 'US'
            ],
            'shipperContact' => [
                'company_name' => 'Company Name',
                'email' => 'test@example.com',
                'person_name' => 'Person Name',
                'phone' => '123-123-1234',
            ],
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
                'dimensions' => [
                    'width' => 10,
                    'height' => 20,
                    'length' => 15
                ],
                'weight' => [
                    'value' => 22,
                    'units' => 'LB'
                ]
            ]
        ];

        // TODO: Change for using order ID from the database
        $orderId = '1000';
        $result = $this->fedEx->shipment($shipmentDetails);
        $this->fedEx->storeShipment($orderId, $result);
    }
}
