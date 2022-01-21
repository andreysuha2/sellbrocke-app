<?php

namespace App\Services;

use App\Services\SettingService as Config;
use FedEx\ShipService;
use FedEx\ShipService\ComplexType;
use FedEx\ShipService\SimpleType;
use FedEx\TrackService\Request;
use FedEx\TrackService\ComplexType as TrackServiceComplexType;
use FedEx\TrackService\SimpleType as TrackServiceSimpleType;

class FedExService
{
    private $fedExKey;
    private $fedExPassword;
    private $fedExAccountNumber;
    private $fedExMeterNumber;

    public function __construct()
    {
        $this->fedExKey = Config::getParameter("FEDEX_KEY");
        $this->fedExPassword = Config::getParameter("FEDEX_PASSWORD");
        $this->fedExAccountNumber = Config::getParameter("FEDEX_ACCOUNT_NUMBER");
        $this->fedExMeterNumber = Config::getParameter("FEDEX_METER_NUMBER");
    }

    public function shipment($shipmentDetails)
    {
        $userCredential = new ComplexType\WebAuthenticationCredential();
        $userCredential
            ->setKey($this->fedExKey)
            ->setPassword($this->fedExPassword);

        $webAuthenticationDetail = new ComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        $clientDetail = new ComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber($this->fedExAccountNumber)
            ->setMeterNumber($this->fedExMeterNumber);

        $version = new ComplexType\VersionId();
        $version
            ->setMajor($shipmentDetails['version']['major'])
            ->setIntermediate($shipmentDetails['version']['intermediate'])
            ->setMinor($shipmentDetails['version']['minor'])
            ->setServiceId($shipmentDetails['version']['service_id']);

        $shipperAddress = new ComplexType\Address();
        $shipperAddress
            ->setStreetLines([$shipmentDetails['shipperAddress']['line1']])
            ->setCity($shipmentDetails['shipperAddress']['city'])
            ->setStateOrProvinceCode($shipmentDetails['shipperAddress']['state_code'])
            ->setPostalCode($shipmentDetails['shipperAddress']['postal_code'])
            ->setCountryCode($shipmentDetails['shipperAddress']['country_code']);

        $shipperContact = new ComplexType\Contact();
        $shipperContact
            ->setCompanyName($shipmentDetails['shipperContact']['company_name'])
            ->setEMailAddress($shipmentDetails['shipperContact']['email'])
            ->setPersonName($shipmentDetails['shipperContact']['person_name'])
            ->setPhoneNumber(($shipmentDetails['shipperContact']['phone']));

        $shipper = new ComplexType\Party();
        $shipper
            ->setAccountNumber($this->fedExAccountNumber)
            ->setAddress($shipperAddress)
            ->setContact($shipperContact);

        $recipientAddress = new ComplexType\Address();
        $recipientAddress
            ->setStreetLines([$shipmentDetails['recipientAddress']['line1']])
            ->setCity($shipmentDetails['recipientAddress']['city'])
            ->setStateOrProvinceCode($shipmentDetails['recipientAddress']['state_code'])
            ->setPostalCode($shipmentDetails['recipientAddress']['postal_code'])
            ->setCountryCode($shipmentDetails['recipientAddress']['country_code']);

        $recipientContact = new ComplexType\Contact();
        $recipientContact
            ->setPersonName($shipmentDetails['recipientContact']['person_name'])
            ->setPhoneNumber($shipmentDetails['recipientContact']['phone']);

        $recipient = new ComplexType\Party();
        $recipient
            ->setAddress($recipientAddress)
            ->setContact($recipientContact);

        $labelSpecification = new ComplexType\LabelSpecification();
        $labelSpecification
            ->setLabelStockType(new SimpleType\LabelStockType(SimpleType\LabelStockType::_PAPER_7X4POINT75))
            ->setImageType(new SimpleType\ShippingDocumentImageType(SimpleType\ShippingDocumentImageType::_PDF))
            ->setLabelFormatType(new SimpleType\LabelFormatType(SimpleType\LabelFormatType::_COMMON2D));

        $units = $shipmentDetails['package']['weight']['units'] === 'LBS' ? SimpleType\WeightUnits::_LB : SimpleType\WeightUnits::_KG;

        $packageLineItem1 = new ComplexType\RequestedPackageLineItem();
        $packageLineItem1
            ->setSequenceNumber(1)
            ->setItemDescription('Electronics')
            ->setWeight(new ComplexType\Weight(array(
                'Value' => $shipmentDetails['package']['weight']['value'],
                'Units' => $units
            )));

        $shippingChargesPayor = new ComplexType\Payor();
        $shippingChargesPayor->setResponsibleParty($shipper);

        $shippingChargesPayment = new ComplexType\Payment();
        $shippingChargesPayment
            ->setPaymentType(SimpleType\PaymentType::_SENDER)
            ->setPayor($shippingChargesPayor);

        $requestedShipment = new ComplexType\RequestedShipment();
        $requestedShipment->setShipTimestamp(date('c'));
        $requestedShipment->setDropoffType(new SimpleType\DropoffType(SimpleType\DropoffType::_REGULAR_PICKUP));
        $requestedShipment->setServiceType(new SimpleType\ServiceType(SimpleType\ServiceType::_FEDEX_GROUND));
        $requestedShipment->setPackagingType(new SimpleType\PackagingType(SimpleType\PackagingType::_YOUR_PACKAGING));
        $requestedShipment->setShipper($shipper);
        $requestedShipment->setRecipient($recipient);
        $requestedShipment->setLabelSpecification($labelSpecification);
        $requestedShipment->setRateRequestTypes(array(new SimpleType\RateRequestType(SimpleType\RateRequestType::_PREFERRED)));
        $requestedShipment->setPackageCount(1);
        $requestedShipment->setRequestedPackageLineItems([
            $packageLineItem1
        ]);
        $requestedShipment->setShippingChargesPayment($shippingChargesPayment);

        $processShipmentRequest = new ComplexType\ProcessShipmentRequest();
        $processShipmentRequest->setWebAuthenticationDetail($webAuthenticationDetail);
        $processShipmentRequest->setClientDetail($clientDetail);
        $processShipmentRequest->setVersion($version);
        $processShipmentRequest->setRequestedShipment($requestedShipment);

        $shipService = new ShipService\Request();
        return $shipService->getProcessShipmentReply($processShipmentRequest)->toArray();
    }

    public function tracking($trackingNumber)
    {
        $trackRequest = new TrackServiceComplexType\TrackRequest();

        $userCredential = new TrackServiceComplexType\WebAuthenticationCredential();
        $userCredential->setKey($this->fedExKey)
            ->setPassword($this->fedExPassword);

        $clientDetail = new TrackServiceComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber($this->fedExAccountNumber)
            ->setMeterNumber($this->fedExMeterNumber);

        $webAuthenticationDetail = new TrackServiceComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        $version = new TrackServiceComplexType\VersionId();
        $version
            ->setMajor(19)
            ->setIntermediate(0)
            ->setMinor(0)
            ->setServiceId('trck');

        $trackRequest->setWebAuthenticationDetail($webAuthenticationDetail);
        $trackRequest->setClientDetail($clientDetail);
        $trackRequest->setVersion($version);

        $trackRequest->SelectionDetails = [new TrackServiceComplexType\TrackSelectionDetail()];

        // For get all events
        $trackRequest->ProcessingOptions = [TrackServiceSimpleType\TrackRequestProcessingOptionType::_INCLUDE_DETAILED_SCANS];

        // Track shipment 1
        $trackRequest->SelectionDetails[0]->PackageIdentifier->Value = $trackingNumber;
        $trackRequest->SelectionDetails[0]->PackageIdentifier->Type = TrackServiceSimpleType\TrackIdentifierType::_TRACKING_NUMBER_OR_DOORTAG;

        // $trackRequest
        $request = new Request();
        $result = $request->getTrackReply($trackRequest);

        $data = [];
        $events = [];

        if (isset($result) && isset($result->CompletedTrackDetails[0])) {
            $data['details'] = [
                'severity' => $result->CompletedTrackDetails[0]->TrackDetails[0]->Notification->Severity,
                'source' => $result->CompletedTrackDetails[0]->TrackDetails[0]->Notification->Source,
                'message' => $result->CompletedTrackDetails[0]->TrackDetails[0]->Notification->Message,
                'trackingNumber' => $result->CompletedTrackDetails[0]->TrackDetails[0]->TrackingNumber
            ];
        }

        if (isset($result->CompletedTrackDetails[0]->TrackDetails[0]->Events)) {
            $trackEvents = $result->CompletedTrackDetails[0]->TrackDetails[0]->Events;

            foreach ($trackEvents as $event) {
                $events[] = [
                    'timestamp' => (new \DateTime($event->Timestamp))->format('Y-m-d g:i a'),
                    'eventType' => $event->EventType,
                    'eventDescription' => $event->EventDescription,
                    'address' => [
                        'city' => $event->Address->City,
                        'stateOrProvinceCode' => $event->Address->StateOrProvinceCode,
                        'postalCode' => $event->Address->PostalCode,
                        'countryCode' => $event->Address->CountryCode,
                        'countryName' => $event->Address->CountryName,
                        'residential' => $event->Address->Residential
                    ]
                ];
            }

            $data['events'] = $events;
        }

        return $data;
    }
}
