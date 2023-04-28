<?php
namespace OBY\GHTK\Api; 

interface ApiInterface
{
    /**
     * @param string $orderId
     * @return string
     */
    public function createOrder($orderId);
}