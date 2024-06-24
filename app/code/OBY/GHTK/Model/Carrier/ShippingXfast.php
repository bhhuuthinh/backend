<?php
namespace OBY\GHTK\Model\Carrier;

use Magento\Shipping\Model\Carrier\CarrierInterface;

class ShippingXfast extends Shipping implements CarrierInterface
{
    const CODE = 'ghtkxfast';
    const DELIVER_OPTION = 'xteam';
    protected $_code = self::CODE;
}
