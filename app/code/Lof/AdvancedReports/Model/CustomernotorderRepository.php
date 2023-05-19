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
use Lof\AdvancedReports\Api\CustomernotorderInterface;
use Lof\AdvancedReports\Model\AbstractReport;
use Magento\Framework\Api\SortOrder;
 
class CustomernotorderRepository extends AbstractReport implements CustomernotorderInterface
{
    protected $_limit = 10;
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnDate = 'main_table.created_at';
     protected $_defaultSort = 'e.entity_id';
    protected $_defaultDir = 'DESC';

    public function __construct(   
        \Lof\AdvancedReports\Helper\Data $helperData, 
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Lof\AdvancedReports\Helper\Api\Datefield $helperDatefield, 
        \Lof\AdvancedReports\Api\Data\CustomernotorderdataInterfaceFactory $searchResultsFactory
        )
    {
        $this->searchResultsFactory = $searchResultsFactory;
        parent::__construct($helperData, $objectManager, $storeManager, $localeCurrency, $searchCriteriaBuilder, $localeLists, $helperDatefield);
    }
    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Magento\Customer\Model\ResourceModel\Customer\Collection';
    }

    /**
    * @return \Magento\Framework\DataObject
    */
    public function initFilterData($filter_field = []) {
        $requestData = [];
        $lofFilter = isset($filter_field['lofFilter'])?isset($filter_field['lofFilter']):null;
        $storeIds = isset($filter_field['storeIds'])?isset($filter_field['storeIds']):null;

        if($lofFilter) {
            $requestData = $this->_objectManager->get(
                'Magento\Backend\Helper\Data'
            )->prepareFilterString(
                $lofFilter
            );
        }

        $requestData['store_ids'] = $storeIds;

        if(!isset($requestData['report_field']) || !$requestData['report_field']) {
          $requestData['report_field'] = isset($filter_field['report_field'])?$filter_field['report_field']:$this->_columnDate;
        }
        if(!isset($requestData['filter_from']) || !$requestData['filter_from']) {
          $requestData['filter_from'] = isset($filter_field['filter_from'])?$filter_field['filter_from']:"";
        }
        if(!isset($requestData['filter_to']) || !$requestData['filter_to']) {
          $requestData['filter_to'] = isset($filter_field['filter_to'])?$filter_field['filter_to']:"";
        }

        if(!isset($requestData['customer_email']) || !$requestData['customer_email']) {
          $requestData['customer_email'] = isset($filter_field['customer_email'])?$filter_field['customer_email']:"";
        }
        
        if(!isset($requestData['group_by']) || !$requestData['group_by']) {
          $requestData['group_by'] = isset($filter_field['group_by'])?$filter_field['group_by']:"month";
        }
        if(!isset($requestData['show_actual_columns']) || !$requestData['show_actual_columns']) {
          $requestData['show_actual_columns'] = isset($filter_field['show_actual_columns'])?$filter_field['show_actual_columns']:0;
        }
        if(!isset($requestData['show_order_statuses']) || ($requestData['show_order_statuses'] == NULL && $requestData['show_order_statuses'] == "")) {
          $requestData['show_order_statuses'] = isset($filter_field['show_order_statuses'])?(int)$filter_field['show_order_statuses']:1;;
        }

        if(!isset($requestData['order_statuses'])) {
            $requestData['order_statuses'] =  isset($filter_field['order_statuses'])?$filter_field['order_statuses']:"complete";
        }
        if($requestData['show_order_statuses'] == 0) {
            $requestData['order_statuses'] = "";
        }

        $params = new \Magento\Framework\DataObject();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
        $this->setFilterData($params);

        return $params;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\AdvancedReports\Api\Data\CustomernotorderdataInterface
     */
    public function getCustomerNotOrder(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
        $cur_month = date("m");
        $cur_year = date("Y");
        $filter_fields = [
                        "show_order_statuses"=>1,
                        "filter_from"=>$cur_month."/01/".$cur_year,
                        "filter_to"=>date("m/d/Y"),
                        "order_statuses"=>"complete",
                        "show_actual_columns"=>1,
                        "period_type"=>"",
                        "customer_email"=>"",
                        "group_by"=>"month",
                        "report_field"=>"main_table.created_at",
                        "lofFilter"=>"",
                        "storeIds"=>""];

        //Convert search criteria to specify filter params
        
        foreach ($searchCriteria->getFilterGroups() as $group) {
            if(!$group)
                continue;
            //var \Magento\Framework\Api\Search\FilterGroup $group
            foreach ($group->getFilters() as $filter) {
                $field = $filter->getField();
                $value = $filter->getValue();
                if($field != "filter_groups" && $field != "sort_orders" && isset($filter_fields[$field])) {
                    $filter_fields[$field] = $value;
                }
            }
        }

        //Init filter data to convert into a filter object of the report
        $this->initFilterData($filter_fields);

        $filterData = $this->getFilterData();
        $store_ids = $this->_getStoreIds();
  
        $resourceCollection = $this->_objectManager->create($this->getResourceCollectionName())
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $resourceCollection = $this->_applyStoresFilterToSelect($resourceCollection); 

        //Order Collection
        $resourceOrderCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Customer\Order\Collection')
            ->prepareListOrderCustomersCollection()
            ->setDateColumnFilter('created_at')
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($store_ids);

        $resourceOrderCollection->addOrderStatusFilter($filterData->getData('order_statuses'));

        $resourceOrderCollection->getSelect()
                            ->group('customer_id'); 
        $resourceOrderCollection->applyCustomFilter();
        $order_select = $resourceOrderCollection->getSelect();
        //End Order Collection

        if ($order_select) {
            $resourceCollection->getSelect()->where("e.entity_id NOT IN (?)", $order_select);
        }
   
        /** @var SortOrder $sortOrder */
        $set_order = false;
        if((array)$searchCriteria->getSortOrders()){
            foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                if($field) {
                    $field = ($field!="period")?$field:'e.entity_id';
                    $set_order = true;
                    $resourceCollection->addOrder(
                        $field,
                        ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                    );
                }
            }
        }

        if(!$set_order) {
            $resourceCollection->getSelect() 
                            ->order(new \Zend_Db_Expr($this->_defaultSort." ".$this->_defaultDir));
        }

        if($currentPage = $searchCriteria->getCurrentPage()) {
            $resourceCollection->setCurPage($currentPage);
        }
        if($pageSize = $searchCriteria->getPageSize()) {
            $resourceCollection->setPageSize($pageSize);
        }


        $resourceCollection->load();
        $this->_convertGridData($resourceCollection);
        //init \Lof\AdvancedReports\Api\Data\CustomerreportdataInterface
        $searchResult = $this->searchResultsFactory->create();

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($resourceCollection->getItems());
        $searchResult->setTotalCount($resourceCollection->getSize());
        return $searchResult;
    }

    /**
     * Apply stores filter to select object
     *
     * @param Zend_Db_Select $select
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function _applyStoresFilterToSelect($resourceCollection )
    {
        $nullCheck = false;
        $storeIds  = $this->_getStoreIds();
        $select = $resourceCollection->getSelect();

        if($storeIds) {
            if (!is_array($storeIds)) {
                $storeIds = array($storeIds);
            }

            $storeIds = array_unique($storeIds);

            if ($index = array_search(null, $storeIds)) {
                unset($storeIds[$index]);
                $nullCheck = true;
            }

            $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

            if ($nullCheck) {
                $select->where('e.store_id IN(?) OR e.store_id IS NULL', $storeIds);
            } else {
                $select->where('e.store_id IN(?)', $storeIds);
            }
        }
        return $resourceCollection;
    }

    protected function _getPeriodType () {
        return "main_table.customer_id";
    } 

    protected function _convertGridData(&$collection = null) {

        if($collection && $collection->getSize()) {
            $filterData = $this->getFilterData();
            $groups  = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Group\Collection')
                    ->addFieldToFilter('customer_group_id', array('gt'=> 0))
                    ->load()
                    ->toOptionHash();
            foreach($collection as &$item) {
                $country_code = $item->getBillingCountryId();
                $billing_country_label = $this->localeLists->getCountryTranslation($country_code);
                $billing_country_label = ($billing_country_label?$billing_country_label:$country_code);
                $item->setData("billing_country_label", $billing_country_label);

                $group_id = $item->getGroupId();
                if($group_id && isset($groups[$group_id])) {
                    $group_label = $groups[$group_id];
                    $item->setData("group_label", $group_label);
                }
            }
        }
        return $collection;
    }
}