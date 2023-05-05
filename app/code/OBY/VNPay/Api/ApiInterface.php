<?php
namespace OBY\VNPay\Api; 

interface ApiInterface
{
    /**
     * @param string $orderId
     * @return string
     */
    public function createOrder($orderId);

    /**
     * @return string
    */
    public function ipn();
}