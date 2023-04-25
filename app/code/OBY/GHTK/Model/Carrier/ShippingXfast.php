<?php
namespace OBY\GHTK\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use OBY\GHTK\Model\ApiCall;

class ShippingXfast extends Shipping implements CarrierInterface
{
    const CODE = 'ghtk_xfast';
    protected $_code = self::CODE;
    protected $_request;
    protected $_result;
    protected $_baseCurrencyRate;
    protected $_xmlAccessRequest;
    protected $_localeFormat;
    protected $_logger;
    protected $configHelper;
    protected $_errors = [];
    protected $_isFixed = true;
    
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
    }

    public function getAllowedMethods()
    {
    }
    
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $instance   = new ApiCall($this->getConfigData('base_url'), $this->getConfigData('token_key'));
        $res        = $instance->ServicesShipmentFee([
            'pick_address_id'	=> 16927840,
			'address'			=> $request->getDestStreet(),
			'district'			=> $request->getDestRegionCode(),
			'province'			=> $request->getDestCity(),
			'weight'			=> $request->getPackageWeight(),
			// 'value'				=> 100000,
			'deliver_option'	=> 'xteam',
        ]);
		$shipment_fee				= $res->fee->fee;

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($shipment_fee);
        $method->setCost($shipment_fee);

        $result->append($method);

        return $result;
    }
    
    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request) {
        return true;
    }
}