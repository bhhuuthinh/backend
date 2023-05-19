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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\AdvancedReports\Model;

/**
 * AdvancedReports Model
 */
class AbstractReport
{
    protected $_filterData;
    protected $_helperData;
    protected $_helperDatefield;
    protected $_objectManager;
    protected $_storeManager;
    protected $_resourceCollectionName  = '';
    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = '';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Lof\AdvancedReports\Api\Data\ResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    public $localeLists;

    public function __construct(
        \Lof\AdvancedReports\Helper\Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Lof\AdvancedReports\Helper\Api\Datefield $helperDatefield
        )
    {
        $this->_helperData = $helperData;
        $this->_helperDatefield = $helperDatefield;
        $this->_storeManager = $storeManager;
        $this->localeLists = $localeLists;
        $this->_objectManager = $objectManager;
        $this->_localeCurrency = $localeCurrency;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function formatCurrency($price, $websiteId = null)
    {
        return $this->_storeManager->getWebsite($websiteId)->getBaseCurrency()->format($price);
    }
    public function getResourceCollectionName()
    {
        return $this->_resourceCollectionName;
    }

    public function setFilterData($params ) {
        $this->_filterData = $params;
    }
    public function getFilterData() {
        return $this->_filterData;
    }

    public function setCulumnDate($_columnDate = "") {
        if($_columnDate) {
            $this->_columnDate = $_columnDate;
        }
    }

    public function setDefaultSort($_columnSort = "") {
        if($_columnSort) {
            $this->_defaultSort = $_columnSort;
        }
    }

    public function setDefaultDir($_dir = "") {
        if($_dir) {
            $this->_defaultDir = $_dir;
        }
    }

    /**
     * Add order status filter
     *
     * @param Mage_Reports_Model_Resource_Report_Collection_Abstract $collection
     * @param Varien_Object $filterData
     * @return Mage_Adminhtml_Block_Report_Grid_Abstract
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        $collection->addOrderStatusFilter($filterData->getData('order_statuses'));
        return $this;
    }
    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return  mixed
     */
    protected function _getStoreIds()
    {
        $filterData = $this->getFilterData();
        if ($filterData) {
            $storeIds = explode(',', $filterData->getData('store_ids'));
        } else {
            $storeIds = array();
        }
        // By default storeIds array contains only allowed stores
        $allowedStoreIds = array_keys($this->_storeManager->getStores());
        // And then array_intersect with post data for prevent unauthorized stores reports
        $storeIds = array_intersect($allowedStoreIds, $storeIds);
        // If selected all websites or unauthorized stores use only allowed
        if (empty($storeIds)) {
            $storeIds = $allowedStoreIds;
        }
        // reset array keys
        $storeIds = array_values($storeIds);

        return $storeIds;
    }
}
