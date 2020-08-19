<?php

namespace App\Http\Controllers;

use App\Services\UPSService;

class UPSController extends Controller
{
    public $UPSService;

    public function __construct(UPSService $UPSService)
    {
        $this->UPSService = $UPSService;
    }

    public function shipmentRequest()
    {
        $packages = [
            [
                "Description" => "International Goods",
                "Packaging" => [
                    "Code" => "02"
                ],
                "PackageWeight" => [
                    "UnitOfMeasurement" => [
                        "Code" => "LBS"
                    ],
                    "Weight" => "10"
                ],
                "PackageServiceOptions" => ""
            ],
            [
                "Description" => "International Goods",
                "Packaging" => [
                    "Code" => "02"
                ],
                "PackageWeight" => [
                    "UnitOfMeasurement" => [
                        "Code" => "LBS"
                    ],
                    "Weight" => "20"
                ],
                "PackageServiceOptions" => ""
            ]
        ];

        $shipmentDetails = [
            "ShipmentRequest" => [
                "Shipment" => [
                    "Description" => "1206 PTR",
                    "Shipper" => [
                        "Name" => "John Doe",
                        "AttentionName" => "AttentionName",
                        "TaxIdentificationNumber" => "TaxID",
                        "Phone" => [
                            "Number" => "1234567890"
                        ],
                        "ShipperNumber" => "84R957",
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
                        "TaxIdentificationNumber" => "456999",
                        "Address" => [
                            "AddressLine" => "Sixth Avenue",
                            "City" => "New York",
                            "StateProvinceCode" => "NY",
                            "PostalCode" => "10001",
                            "CountryCode" => "US"
                        ]
                    ],
                    "ShipFrom" => [
                        "Name" => "ShipperName",
                        "AttentionName" => "AttentionName",
                        "Phone" => [
                            "Number" => "1234567890"
                        ],
                        "FaxNumber" => "1234567999",
                        "TaxIdentificationNumber" => "456999",
                        "Address" => [
                            "AddressLine" => "Saks Fifth Avenue",
                            "City" => "New York",
                            "StateProvinceCode" => "NY",
                            "PostalCode" => "10001",
                            "CountryCode" => "US"
                        ]
                    ],
                    "PaymentInformation" => [
                        "ShipmentCharge" => [
                            "Type" => "01",
                            "BillShipper" => [
                                "AccountNumber" => "84R957"
                            ]
                        ]
                    ],
                    "Service" => [
                        "Code" => "01",
                        "Description" => "Expedited"
                    ],
                    "Package" => $packages,
                    "ItemizedChargesRequestedIndicator" => "",
                    "RatingMethodRequestedIndicator" => "",
                    "TaxInformationIndicator" => "",
                    "ShipmentRatingOptions" => [
                        "NegotiatedRatesIndicator" => ""
                    ]
                ],
                "LabelSpecification" => [
                    "LabelImageFormat" => [
                        "Code" => "GIF"
                    ]
                ]
            ]
        ];

        // TODO: Change for using order ID from the database
        $orderId = '1000';
        $response = $this->UPSService->shipment($shipmentDetails);
        $this->UPSService->storeShipment($orderId, $response);
    }

    /**
     * The method gets shipment label from the UPS API
     * Note: in the development mode use $trackingNumber = 1Z12345E8791315413
     *
     * @param string $trackingNumber
     */
    public function labelRecovery($trackingNumber)
    {
        $shipmentDetails = [
            "LabelRecoveryRequest" => [
                "LabelSpecification" => [
                    "HTTPUserAgent" => "",
                    "LabelImageFormat" => [
                        "Code" => "GIF"
                    ]
                ],
                "Translate" => [
                    "LanguageCode" => "eng",
                    "DialectCode" => "US",
                    "Code" => "01"
                ],
                "LabelDelivery" => [
                    "LabelLinkIndicator" => "",
                    "ResendEMailIndicator" => "",
                    "EMailMessage" => [
                        "EMailAddress" => ""
                    ]
                ],
                "TrackingNumber" => $trackingNumber,
                "ReferenceValue" => [
                    "ShipperNumber" => "84R957"
                ]
            ]
        ];

        $response = $this->UPSService->label($shipmentDetails);
        $this->UPSService->storeShipmentLabel($response);
    }

    /**
     * The method cancels shipment by Shipment Identification Number
     * Note: in the development mode need to use $shipmentIdentificationNumber = 1ZISDE016691676846
     *
     * @param $shipmentIdentificationNumber
     */
    public function shipmentCancel($shipmentIdentificationNumber)
    {
        $response = $this->UPSService->cancel($shipmentIdentificationNumber);
        $this->UPSService->storeShipmentCancel($shipmentIdentificationNumber, $response);
    }
}
