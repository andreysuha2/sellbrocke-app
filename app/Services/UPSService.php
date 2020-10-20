<?php

namespace App\Services;

use App\Interfaces\UPSInterface;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use League\Flysystem\Exception;


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
        
        if ($response->serverError() || $response->clientError()) {
            throw new Exception($response);
        }

        if ($response->ok()) {
            return $response->json();
        }

        return null;
    }

    public function shipment($shipmentDetails)
    {
        $url = env('UPS_URL') . '/ship/v1/shipments?additionaladdressvalidation=city';

        return $this->request($url, $shipmentDetails);
    }

    public function cancel($shipmentIdentificationNumber)
    {
        if (empty($shipmentIdentificationNumber)) {
            return null;
        }

        $url = env('UPS_URL') . "/ship/v1/shipments/cancel/{$shipmentIdentificationNumber}";

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
        $url = env('UPS_URL') . '/ship/v1/shipments/labels';

        return $this->request($url, $shipmentDetails);
    }

    public function tracking($trackingNumber)
    {
        $url = env('UPS_URL') . "/track/v1/details/{$trackingNumber}";

        $response = Http::withHeaders(
            $this->headers
        )->get($url);

        if ($response->serverError() || $response->clientError()) {
            throw new Exception($response);
        }

        if ($response->ok()) {
            $response = $response->json();

            if (!isset($response) && !isset($response['trackResponse'])) {
                return null;
            }

            if (!isset($response['trackResponse']['shipment'][0]['package'][0]['activity'])) {
                return null;
            }

            $events = $response['trackResponse']['shipment'][0]['package'][0]['activity'];

            $data = [];
            foreach ($events as $event) {
                $data[] = [
                    'timestamp' => date('Y-m-d', strtotime($event['date'])) . " " . date('g:i a', $event['time']),
                    'eventType' => $event['status']['type'],
                    'eventDescription' => $event['status']['description'],
                    'address' => [
                        'city' => $event['location']['address']['city'],
                        'stateOrProvinceCode' => $event['location']['address']['stateProvince'],
                        'countryCode' => $event['location']['address']['country']
                    ]
                ];
            }

            return $data;
        }

        return null;
    }

}
