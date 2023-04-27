<?php
namespace OBY\Momo\Api; 

interface ApiInterface
{
    /**
     * @param string $value
     * @return object
     */
    public function captureWallet($orderId);

    /**
     * @return object
    */
    public function ipn();
}