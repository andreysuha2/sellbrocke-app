<?php

namespace App\Services;

use App\Interfaces\UPSInterface;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Exception;
use App\Models\UPSPackage;
use App\Models\UPSShipment;


class UPSService implements UPSInterface
{
    private $accessKey;
    private $userID;
    private $userPassword;
    protected $http;
    protected $headers;

    public function __construct(Client $client)
    {
        $this->accessKey = getenv("UPS_ACCESS_KEY");
        $this->userID = getenv("UPS_USER_ID");
        $this->userPassword = getenv("UPS_PASSWORD");
        $this->http = $client;

        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'AccessLicenseNumber' => $this->accessKey,
            'Username' => $this->userID,
            'Password' => $this->userPassword,
            'transId' => 'Transaction123',
            'transactionSrc' => 'GG'
        ];
    }

    private function request($url, $shipmentDetails)
    {
        $response = Http::withHeaders(
            $this->headers
        )->withBody(
            json_encode($shipmentDetails),
            'application/json'
        )->post($url);
        
        if ($response->serverError()) {
            throw new Exception('Error: 500 Internal Server Error');
        }

        if ($response->clientError()) {
            throw new Exception('Error: 400 Bad request');
        }

        if ($response->ok()) {
            return $response->json();
        }

        return null;
    }

    public function shipment($shipmentDetails)
    {
        $url = 'https://wwwcie.ups.com/ship/v1/shipments?additionaladdressvalidation=city';

        return $this->request($url, $shipmentDetails);
    }

    public function cancel($shipmentIdentificationNumber)
    {
        if (empty($shipmentIdentificationNumber)) {
            return null;
        }

        $url = "https://wwwcie.ups.com/ship/v1/shipments/cancel/{$shipmentIdentificationNumber}";

        $response = Http::withHeaders(
            $this->headers
        )->delete($url);

        if ($response->serverError()) {
            throw new Exception('Error: 500 Internal Server Error');
        }

        if ($response->clientError()) {
            throw new Exception('Error: 400 Bad request');
        }

        if ($response->ok()) {
            return $response->json();
        }

        return null;
    }

    public function label($shipmentDetails)
    {
        $url = 'https://wwwcie.ups.com/ship/v1/shipments/labels';

        return $this->request($url, $shipmentDetails);
    }

    public function storeShipment($orderId, $shipmentResponse)
    {
        if ($shipmentResponse && $shipmentResponse['ShipmentResponse']['Response']['ResponseStatus']['Code'] == 1) {

            $shipmentResults = $shipmentResponse['ShipmentResponse']['ShipmentResults'];

            $shipment = new UPSShipment();
            $shipment->order_id = $orderId;
            $shipment->shipment_identification_number = $shipmentResults['ShipmentIdentificationNumber'];
            $shipment->package_count = count($shipmentResults['PackageResults']);
            $shipment->weight = $shipmentResults['BillingWeight']['Weight'];
            $shipment->weight_code = $shipmentResults['BillingWeight']['UnitOfMeasurement']['Code'];
            $shipment->weight_measurement = $shipmentResults['BillingWeight']['UnitOfMeasurement']['Description'];
            $shipment->currency_code = $shipmentResults['ShipmentCharges']['TotalCharges']['CurrencyCode'];
            $shipment->total_charges = $shipmentResults['ShipmentCharges']['TotalCharges']['MonetaryValue'];
            $shipment->status = 'Delivery';
            $shipment->save();

            if ($shipment->id && $shipment->package_count > 0) {
                foreach ($shipmentResults['PackageResults'] as $packageDetails) {
                    $package = new UPSPackage();
                    $package->shipment_id = $shipment->id;
                    $package->tracking_number = $packageDetails['TrackingNumber'];
                    $package->label = $packageDetails['ShippingLabel']['GraphicImage'];
                    $package->save();
                }
            }
        }
    }

    public function storeShipmentCancel($shipmentIdentificationNumber, $shipmentCancelResponse)
    {
        if (empty($shipmentIdentificationNumber) || empty($shipmentCancelResponse)) {
            return null;
        }

        if ($shipmentCancelResponse['VoidShipmentResponse']['Response']['ResponseStatus']['Code'] == 1
            && $shipmentCancelResponse['VoidShipmentResponse']['SummaryResult']['Status']['Code'] == 1) {

            $shipment = UPSShipment::where('shipment_identification_number', '=', $shipmentIdentificationNumber)->first();

            $shipment->status = $shipmentCancelResponse['VoidShipmentResponse']['SummaryResult']['Status']['Description'];
            $shipment->save();
        }
    }

    /**
     * The method gets shipment label and return label image file URL
     *
     * @param array $shipmentLabelResponse
     * @return string Label image file URL
     */
    public function storeShipmentLabel($shipmentLabelResponse)
    {
        if ($shipmentLabelResponse
            && $shipmentLabelResponse['LabelRecoveryResponse']['Response']['ResponseStatus']['Code'] == 1) {

            $fileName = "label-{$shipmentLabelResponse['LabelRecoveryResponse']['ShipmentIdentificationNumber']}.gif";
            $imageData = $shipmentLabelResponse['LabelRecoveryResponse']['LabelResults']['LabelImage']['GraphicImage'];
            $imageData = str_replace(' ', '+', $imageData);
            Storage::disk('local')->put("public/{$fileName}", base64_decode($imageData));

            return asset("storage/{$fileName}");
        }
    }
}