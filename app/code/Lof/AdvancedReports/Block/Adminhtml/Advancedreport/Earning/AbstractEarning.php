<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_AdvancedReports
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Earning;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class AbstractEarning
 *
 * @package Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Earning
 */
class AbstractEarning extends \Magento\Backend\Block\Template {
    protected $_columnDate = 'main_table.created_at';
    protected $_limit = 10;
    protected $_storeManager;
    protected $_helperData;
    protected $_objectManager;
    protected $_registry;
    protected $_localeCurrency;
    protected $_storeIds = [];
    protected $_currentCurrencyCode = null;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    public $localeLists;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_pricingHelper;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    protected $_priceCurrency;

    /**
     * AbstractEarning constructor.
     *
     * @param \Magento\Backend\Block\Template\Context     $context
     * @param \Lof\AdvancedReports\Helper\Data            $helperData
     * @param \Magento\Framework\Registry                 $registry
     * @param \Magento\Framework\ObjectManagerInterface   $objectManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\Locale\ListsInterface    $localeLists
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lof\AdvancedReports\Helper\Data $helperData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->localeLists     = $localeLists;
        $this->_helperData     = $helperData;
        $this->_storeManager   = $context->getStoreManager();
        $this->_objectManager  = $objectManager;
        $this->_registry       = $registry;
        $this->_localeCurrency = $localeCurrency;
        $this->_pricingHelper  = $pricingHelper;
        $this->_priceCurrency  = $priceCurrency;
    }

    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;

        return $this;
    }

    /**
     * @param      $price
     * @param null $websiteId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function formatCurrency($price, $websiteId = null)
    {
        $filterData   = $this->getFilterData();
        $currencyCode = $filterData->getData('currency_code') ?: null;

        return $this->_priceCurrency->convertAndFormat($price, true, PriceCurrencyInterface::DEFAULT_PRECISION, null, $currencyCode);

        // return $this->_storeManager->getWebsite( $websiteId )->getBaseCurrency()->format( $price );
    }

    /**
     * @param float $price
     * @return string
     */
    public function getProductPriceHtml($price)
    {
        return $this->_pricingHelper->currency($price, true, false);
    }

    /**
     * @param null $currency_code
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentCurrencyCode($currency_code = null)
    {
        if ($currency_code) {
            return $currency_code;
        }

        if ($this->_currentCurrencyCode === null) {
            $this->_currentCurrencyCode = count($this->_storeIds) > 0
                ? $this->_storeManager->getStore(array_shift($this->_storeIds))->getBaseCurrencyCode()
                : $this->_storeManager->getStore()->getBaseCurrencyCode();
        }

        return $this->_currentCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Earning\Collection';
    }
}
