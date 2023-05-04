<?php
namespace OBY\VNPay\Api; 

interface ApiInterface
{
    /**
     * @param string $orderId
     * @return string
     */
    public function captureWallet($orderId);

    /**
     * @return string
    */
    public function ipn();
}