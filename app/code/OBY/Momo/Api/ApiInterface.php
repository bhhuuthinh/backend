<?php
namespace OBY\Momo\Api; 

interface ApiInterface
{
    /**
     * @param string $value
     * @return string
     */
    public function captureWallet($orderId);

    /**
     * @return void
    */
    public function ipn();
}