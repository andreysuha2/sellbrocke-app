<?php

namespace App\Services;

use FedEx\ShipService;
use FedEx\ShipService\Request;
use FedEx\ShipService\ComplexType;
use FedEx\ShipService\SimpleType;
use App\Models\FedExShipment;

class FedExService
{
    public function shipment($shipmentDetails)
    {
        $userCredential = new ComplexType\WebAuthenticationCredential();
        $userCredential
            ->setKey(getenv('FEDEX_KEY'))
            ->setPassword(getenv('FEDEX_PASSWORD'));

        $webAuthenticationDetail = new ComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        $clientDetail = new ComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber(getenv('FEDEX_ACCOUNT_NUMBER'))
            ->setMeterNumber(getenv('FEDEX_METER_NUMBER'));

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
            ->setAccountNumber(getenv('FEDEX_ACCOUNT_NUMBER'))
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

        $packageLineItem1 = new ComplexType\RequestedPackageLineItem();
        $packageLineItem1
            ->setSequenceNumber(1)
            ->setItemDescription('Apple McBook Air')
            ->setDimensions(new ComplexType\Dimensions(array(
                'Width' => $shipmentDetails['package']['dimensions']['width'],
                'Height' => $shipmentDetails['package']['dimensions']['height'],
                'Length' => $shipmentDetails['package']['dimensions']['length'],
                'Units' => SimpleType\LinearUnits::_IN
            )))
            ->setWeight(new ComplexType\Weight(array(
                'Value' => $shipmentDetails['package']['weight']['value'],
                'Units' => SimpleType\WeightUnits::_LB
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
        return $shipService->getProcessShipmentReply($processShipmentRequest);
    }

    public function storeShipment($orderId, $shipmentResponse)
    {
        if ($shipmentResponse->HighestSeverity !== 'SUCCESS') {
            return null;
        }

        if ($shipmentResponse->CompletedShipmentDetail->CompletedPackageDetails
            && count($shipmentResponse->CompletedShipmentDetail->CompletedPackageDetails) > 0) {
            $shipmentPackageDetails = $shipmentResponse->CompletedShipmentDetail->CompletedPackageDetails[0];

            if ($shipmentPackageDetails->PackageRating->PackageRateDetails
                && count($shipmentPackageDetails->PackageRating->PackageRateDetails) > 0) {
                $packageDetails = $shipmentPackageDetails->PackageRating->PackageRateDetails[0];

                $fedExShipment = new FedExShipment();
                $fedExShipment->order_id = $orderId;
                $fedExShipment->job_id = $shipmentResponse->JobId;
                $fedExShipment->tracking_type = $shipmentResponse->CompletedShipmentDetail->MasterTrackingId->TrackingIdType;
                $fedExShipment->tracking_number = $shipmentResponse->CompletedShipmentDetail->MasterTrackingId->TrackingNumber;
                $fedExShipment->weight = $packageDetails->BillingWeight->Value;
                $fedExShipment->weight_code = $packageDetails->BillingWeight->Units;
                $fedExShipment->currency_code = $packageDetails->NetCharge->Currency;
                $fedExShipment->total_charges = $packageDetails->NetCharge->Amount;
                $fedExShipment->status = 'Delivery';
                $fedExShipment->label = $shipmentResponse->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image;
                $fedExShipment->save();
            }

        }
    }
}