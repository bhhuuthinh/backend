<?php
namespace OBY\GHTK\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Ups\Helper\Config;
use Magento\Framework\Xml\Security;
use OBY\GHTK\Model\ApiCall;

class Shipping extends AbstractGhtk implements CarrierInterface
{
    const CODE = 'ghtk';
    const DELIVER_OPTION = 'none';

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

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        Config $configHelper,
        array $data = []
    ) {
        $this->_localeFormat = $localeFormat;
        $this->configHelper = $configHelper;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // Xfast chỉ áp dụng nội thành HCM và HN
        if(strtolower($request->getDestCity()) != 'thành phố hồ chí minh' && static::DELIVER_OPTION == 'xteam'){
            return false;
        }

        $instance   = new ApiCall($this->getConfigData('base_url'), $this->getConfigData('token_key'));
        $res        = $instance->ServicesShipmentFee([
            'pick_address_id'	=> $this->getConfigValue('pick_address_id'),
			'address'			=> $request->getDestStreet(),
			'district'			=> $request->getDestRegionCode(),
			'province'			=> $request->getDestCity(),
			'weight'			=> $request->getPackageWeight(),
			// 'value'				=> 100000,
			'deliver_option'	=> static::DELIVER_OPTION,
        ]);
		$shipment_fee				= $res->fee->fee ?: -1;

        if($shipment_fee <= 0){
            return false;
        }

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

    public function processAdditionalValidation(\Magento\Framework\DataObject $request) {
        return true;
    }
}
