<?php

namespace App\Services;

use App\Interfaces\UPSInterface;
use App\Services\SettingService as Config;
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
    protected $settings;
    protected $upsUrl;

    public function __construct(Client $client)
    {
        $this->settings = Config::getParametersByGroup('ups');
        $this->accessKey = $this->settings["UPS_ACCESS_KEY"];
        $this->userID = $this->settings["UPS_USER_ID"];
        $this->userPassword = $this->settings["UPS_PASSWORD"];
        $this->upsUrl = $this->settings["UPS_SANDBOX"] ? $this->settings["UPS_SANDBOX_URL"] : $this->settings["UPS_PRODUCTION_URL"];
        $this->http = $client;

        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'AccessLicenseNumber' => $this->accessKey,
            'Username' => $this->userID,
            'Password' => $this->userPassword,
            'transId' => uniqid() . rand(10, 99),
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
            $result = json_decode($response);
            if (isset($result->response->errors[0])) {
                throw new Exception($result->response->errors[0]->message);
            } else {
                throw new Exception('Something went wrong!');
            }
        }

        if ($response->ok()) {
            return $response->json();
        }

        return null;
    }

    public function shipment($shipmentDetails)
    {
        $url = $this->upsUrl . '/ship/v1/shipments?additionaladdressvalidation=city';

        return $this->request($url, $shipmentDetails);
    }

    public function cancel($shipmentIdentificationNumber)
    {
        if (empty($shipmentIdentificationNumber)) {
            return null;
        }

        $url = $this->upsUrl . "/ship/v1/shipments/cancel/{$shipmentIdentificationNumber}";

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
        $url = $this->upsUrl . '/ship/v1/shipments/labels';

        return $this->request($url, $shipmentDetails);
    }

    public function tracking($trackingNumber)
    {
        $url = $this->upsUrl . "/track/v1/details/{$trackingNumber}";

        $response = Http::withHeaders(
            $this->headers
        )->get($url);

        if ($response->serverError() || $response->clientError()) {
            throw new Exception($response);
        }

        if ($response->ok()) {
            $response = $response->json();

            $data = [];
            $events = [];

            if (isset($response) && isset($response['trackResponse'])) {
                $data['details'] = [
                    'message' => $response['trackResponse']['shipment'][0]['warnings'][0]['message'],
                    'trackingNumber' => $trackingNumber
                ];
            }

            if (isset($response['trackResponse']['shipment'][0]['package'][0]['activity'])) {
                $trackEvents = $response['trackResponse']['shipment'][0]['package'][0]['activity'];

                foreach ($trackEvents as $event) {
                    $events[] = [
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

                $data['events'] = $events;
            }

            return $data;
        }

        return null;
    }

}
