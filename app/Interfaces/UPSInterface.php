<?php

namespace App\Interfaces;

interface UPSInterface
{
    /**
     * The method creates a new shipment in the UPS service
     *
     * @param array $shipmentDetails Shipment details
     * @return array|null
     */
    public function shipment($shipmentDetails);

    /**
     * The method cancels existing shipment by 'ShipmentIdentificationNumber'
     *
     * @param string $shipmentIdentificationNumber
     * @return array|null
     */
    public function cancel($shipmentIdentificationNumber);

    /**
     * The method returns shipment label (label are stored 90 days)
     *
     * @param array $shipmentDetails
     * @return array|null
     */
    public function label($shipmentDetails);
}