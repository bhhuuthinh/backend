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
use Lof\AdvancedReports\Api\ItemdetailedInterface;
use Lof\AdvancedReports\Model\AbstractReport;
use Magento\Framework\Api\SortOrder;
 
class ItemdetailedRepository extends AbstractReport implements ItemdetailedInterface
{
    protected $_limit = 10;
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnDate = 'main_table.created_at';
     protected $_defaultSort = 'main_table.created_at';
    protected $_defaultDir = 'DESC';

    public function __construct(   
        \Lof\AdvancedReports\Helper\Data $helperData, 
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Lof\AdvancedReports\Helper\Api\Datefield $helperDatefield, 
        \Lof\AdvancedReports\Api\Data\ItemdetaileddataInterfaceFactory $searchResultsFactory
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
        return 'Lof\AdvancedReports\Model\ResourceModel\Order\Collection';
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

        if(!isset($requestData['increment_id']) || !$requestData['increment_id']) {
          $requestData['increment_id'] = isset($filter_field['increment_id'])?$filter_field['increment_id']:"";
        }
        
        if(!isset($requestData['group_by']) || !$requestData['group_by']) {
          $requestData['group_by'] = isset($filter_field['group_by'])?$filter_field['group_by']:"";
        }

        if(!isset($requestData['sku']) || !$requestData['sku']) {
          $requestData['sku'] = isset($filter_field['sku'])?$filter_field['sku']:"";
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
     * @return \Lof\AdvancedReports\Api\Data\ItemdetaileddataInterface
     */
    public function getItemDetailed(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
        $cur_month = date("m");
        $cur_year = date("Y");
        $filter_fields = [
                        "show_order_statuses"=>1,
                        "filter_from"=>$cur_month."/01/".$cur_year,
                        "filter_to"=>date("m/d/Y"),
                        "order_statuses"=>"complete",
                        "show_actual_columns"=>1,
                        "period_type"=>"",
                        "sku"=>"",
                        "increment_id"=>"",
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
            ->prepareOrderItemDetailedCollection()
            ->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($store_ids);

        $resourceCollection->join(array('payment'=>'sales_order_payment'),'main_table.entity_id=parent_id','method');
        
        $this->_addOrderStatusFilter($resourceCollection, $filterData);

        /** @var SortOrder $sortOrder */
        $set_order = false;
        if((array)$searchCriteria->getSortOrders()){
            foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                if($field) {
                    $field = ($field!="period")?$field:'orders_count';
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

        $resourceCollection->applyCustomFilter();

        $resourceCollection->load();
        $this->_convertGridData($resourceCollection);
        //init \Lof\AdvancedReports\Api\Data\CustomerreportdataInterface
        $searchResult = $this->searchResultsFactory->create();

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($resourceCollection->getItems());
        $searchResult->setTotalCount($resourceCollection->getSize());
        return $searchResult;
    }

    protected function _getPeriodType () {
        return "main_table.customer_id";
    } 

    protected function _convertGridData(&$collection = null) {

        if($collection && $collection->getSize()) {
            $filterData = $this->getFilterData();
            $statues = $this->_objectManager->create('Magento\Sales\Model\Order\Config')->getStatuses();

            $payments  = $this->_objectManager->create('Magento\Payment\Model\Config')->getActiveMethods();
            $methods = array();

            foreach ($payments as $paymentCode=>$paymentModel)
            {     
                $paymentTitle = $this->_helperData
                    ->getScopeConfigValue('payment/'.$paymentCode.'/title');  

                $methods[$paymentCode] = $paymentTitle;
            }

            foreach($collection as &$item) {

                $status = $item->getStatus();
                if($statues && isset($statues[$status])){
                    $item->setData("status_label", $statues[$status]);
                }

                $method = $item->getMethod();
                if($methods && isset($methods[$method])){
                    $item->setData("method_label", $methods[$method]);
                }

                $price_currency = $this->formatCurrency($item->getPrice());
                $item->setData("price_currency", $price_currency);

                $base_price_currency = $this->formatCurrency($item->getBasePrice());
                $item->setData("base_price_currency", $base_price_currency);

                $subtotal_currency = $this->formatCurrency($item->getSubtotal());
                $item->setData("subtotal_currency", $subtotal_currency);

                $discount_currency = $this->formatCurrency($item->getDiscountAmount());
                $item->setData("discount_currency", $discount_currency);

                $tax_currency = $this->formatCurrency($item->getTaxAmount());
                $item->setData("tax_currency", $tax_currency);

                $row_total_currency = $this->formatCurrency($item->getRowTotal());
                $item->setData("row_total_currency", $row_total_currency);

                $row_total_incl_tax_currency = $this->formatCurrency($item->getRowTotalInclTax());
                $item->setData("row_total_incl_tax_currency", $row_total_incl_tax_currency);

                $row_invoiced_currency = $this->formatCurrency($item->getRowInvoiced());
                $item->setData("row_invoiced_currency", $row_invoiced_currency);

                $tax_invoiced_currency = $this->formatCurrency($item->getTaxInvoiced());
                $item->setData("tax_invoiced_currency", $tax_invoiced_currency);

                $row_invoiced_incl_tax_currency = $this->formatCurrency($item->getRowInvoicedInclTax());
                $item->setData("row_invoiced_incl_tax_currency", $row_invoiced_incl_tax_currency);

                $amount_refunded_currency = $this->formatCurrency($item->getAmountRefunded());
                $item->setData("amount_refunded_currency", $amount_refunded_currency);

                $real_tax_refunded_currency = $this->formatCurrency($item->getRealTaxRefunded());
                $item->setData("real_tax_refunded_currency", $real_tax_refunded_currency);

                $row_refunded_incl_tax_currency = $this->formatCurrency($item->getRowRefundedInclTax());
                $item->setData("row_refunded_incl_tax_currency", $row_refunded_incl_tax_currency);

                $total_cost_currency = $this->formatCurrency($item->getTotalCostAmount());
                $item->setData("total_cost_currency", $total_cost_currency);

                $total_revenue_amount_excl_tax_currency = $this->formatCurrency($item->getTotalRevenueAmountExclTax());
                $item->setData("total_revenue_amount_excl_tax_currency", $total_revenue_amount_excl_tax_currency);

                $total_revenue_currency = $this->formatCurrency($item->getTotalRevenueAmount());
                $item->setData("total_revenue_currency", $total_revenue_currency);

                $total_profit_currency = $this->formatCurrency($item->getTotalProfitAmount());
                $item->setData("total_profit_currency", $total_profit_currency);

            }
        }
        return $collection;
    }
}