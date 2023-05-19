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

use Lof\AdvancedReports\Api\StatisticsInterface;
use Lof\AdvancedReports\Model\AbstractReport;
use Magento\Framework\Api\SortOrder;

class StatisticsRepository extends EarningRepository implements StatisticsInterface {
    protected $_limit = 10;
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Earning\Collection';
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function initFilterData($filter_field = [])
    {
        $requestData = [];
        $lofFilter   = isset($filter_field['lofFilter']) ? isset($filter_field['lofFilter']) : null;
        $storeIds    = isset($filter_field['storeIds']) ? isset($filter_field['storeIds']) : null;
        $isCurrent   = isset($filter_field['isCurrent']) ? $filter_field['isCurrent'] : 0;

        if ($lofFilter) {
            $requestData = $this->_objectManager->get(
                'Magento\Backend\Helper\Data'
            )->prepareFilterString(
                $lofFilter
            );
        }

        $requestData['store_ids'] = $storeIds;
        if ( ! isset($requestData['filter_year']) || ! $requestData['filter_year']) {
            $requestData['filter_year'] = isset($filter_field['filter_year']) ? $filter_field['filter_year'] : "";
        }
        if ( ! isset($requestData['filter_month']) || ! $requestData['filter_month']) {
            $requestData['filter_month'] = isset($filter_field['filter_month']) ? $filter_field['filter_month'] : "";
        }
        if ( ! isset($requestData['filter_day']) || ! $requestData['filter_day']) {
            $requestData['filter_day'] = isset($filter_field['filter_day']) ? $filter_field['filter_day'] : "";
        }
        if ( ! isset($requestData['show_order_statuses']) || ($requestData['show_order_statuses'] == null && $requestData['show_order_statuses'] == "")) {
            $requestData['show_order_statuses'] = isset($filter_field['show_order_statuses']) ? (int) $filter_field['show_order_statuses'] : 1;;
        }

        if ($isCurrent) {
            $requestData['filter_year']  = date("Y");
            $requestData['filter_month'] = date("m");
            $requestData['filter_day']   = '';
        }
        if ( ! isset($requestData['order_statuses'])) {
            $requestData['order_statuses'] = isset($filter_field['order_statuses']) ? $filter_field['order_statuses'] : "complete";
        }
        if ($requestData['show_order_statuses'] == 0) {
            $requestData['order_statuses'] = "";
        }

        $params = new \Magento\Framework\DataObject();

        foreach ($requestData as $key => $value) {
            if ( ! empty($value)) {
                $params->setData($key, $value);
            }
        }
        $this->setFilterData($params);

        return $params;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\AdvancedReports\Api\Data\ReportdataInterface
     */
    public function getStatistics(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {

        $filter_fields = [
            "storeIds" => "",
        ];

        //Convert search criteria to specify filter params

        foreach ($searchCriteria->getFilterGroups() as $group) {
            if ( ! $group) {
                continue;
            }
            //var \Magento\Framework\Api\Search\FilterGroup $group
            foreach ($group->getFilters() as $filter) {
                $field = $filter->getField();
                $value = $filter->getValue();
                if ($field != "filter_groups" && $field != "sort_orders" && isset($filter_fields[$field])) {
                    $filter_fields[$field] = $value;
                }
            }
        }
        //Init filter data to convert into a filter object of the report
        $this->initFilterData($filter_fields);

        $filterData = $this->getFilterData();
        $store_ids  = $this->_getStoreIds();

        //Get current month and last month sales
        $params                 = ['store' => $store_ids];
        $currentMonthCollection = $this->_helperData->prepareCollection($params);
        $lastMonthCollection    = $this->_helperData->prepareLastMonthCollection($params);


        //Load lifetime order totals
        $all_collection = $this->_objectManager->create('Magento\Reports\Model\ResourceModel\Order\Collection')
                                               ->calculateSales($store_ids);

        if ($store_ids) {
            $all_collection->addFieldToFilter('store_id', $store_ids);
        }

        $all_collection->load();
        $sales           = $all_collection->getFirstItem();
        $all_sales_total = $sales->getLifetime();
        $currencyCode    = $this->getCurrentCurrencyCode(null);
        //Get bestsellers sales
        $bestseller_collection = $this->_objectManager->create($this->getResourceCollectionName())
                                                      ->setOrderRate($currencyCode)
                                                      ->prepareBestsellersCollection()
                                                      ->setDateColumnFilter($this->_columnDate)
                                                      ->addYearFilter($filterData->getData('filter_year', null))
                                                      ->addMonthFilter($filterData->getData('filter_month', null))
                                                      ->addDayFilter($filterData->getData('filter_day', null));

        if ($store_ids) {
            $bestseller_collection->addStoreFilter($store_ids);
        }

        $bestseller_collection->join(['order_item' => 'sales_order_item'], 'main_table.entity_id=order_id', ['product_id', 'order_item.name', 'order_item.price']);
        $bestseller_collection->addOrderStatusFilter($filterData->getData('order_statuses'));
        $bestseller_collection->applyCustomFilter();
        $bestseller_collection->getSelect()
                              ->group('product_id')
                              ->order(new \Zend_Db_Expr('qty_ordered DESC'))
                              ->limit($this->_limit);
        $bestsellers = $this->getBestsellerItems($bestseller_collection);

        //Get top countries sales
        $topcountries_collection = $this->_objectManager->create($this->getResourceCollectionName())
                                                        ->setOrderRate($currencyCode)
                                                        ->prepareReportCollection()
                                                        ->setDateColumnFilter($this->_columnDate)
                                                        ->addYearFilter($filterData->getData('filter_year', null))
                                                        ->addMonthFilter($filterData->getData('filter_month', null))
                                                        ->addDayFilter($filterData->getData('filter_day', null));

        if ($store_ids) {
            $topcountries_collection->addStoreFilter($store_ids);
        }

        $topcountries_collection->join(['address' => 'sales_order_address'], 'main_table.entity_id=parent_id', 'country_id');
        $topcountries_collection->addOrderStatusFilter($filterData->getData('order_statuses'));
        $topcountries_collection->applyCustomFilter();
        $topcountries_collection->getSelect()
                                ->group('address.country_id')
                                ->order(new \Zend_Db_Expr('total_revenue_amount DESC'))
                                ->limit($this->_limit);
        $topcountries = $this->getTopcountriesItems($topcountries_collection);

        //Get top payments sales
        $payment_collection = $this->_objectManager->create($this->getResourceCollectionName())
                                                   ->setOrderRate($currencyCode)
                                                   ->prepareReportCollection()
                                                   ->setDateColumnFilter($this->_columnDate)
                                                   ->addYearFilter($filterData->getData('filter_year', null))
                                                   ->addMonthFilter($filterData->getData('filter_month', null))
                                                   ->addDayFilter($filterData->getData('filter_day', null));

        if ($store_ids) {
            $payment_collection->addStoreFilter($store_ids);
        }

        $payment_collection->join(['payment' => 'sales_order_payment'], 'main_table.entity_id=parent_id', 'method');
        $payment_collection->addOrderStatusFilter($filterData->getData('order_statuses'));
        $payment_collection->applyCustomFilter();
        $payment_collection->getSelect()
                           ->group('payment.method')
                           ->order(new \Zend_Db_Expr('total_revenue_amount DESC'))
                           ->limit($this->_limit);
        $toppayments = $this->getTopPaymentItems($payment_collection);

        //init array
        $results = [];
        //Set current month total and earnings
        $results[0]['type']             = "current_month";
        $results[0]['total']            = $currentMonthCollection->getQuantity();
        $results[0]['earning']          = $currentMonthCollection->getRevenue();
        $results[0]['earning_currency'] = $this->formatCurrency($currentMonthCollection->getRevenue());
        //Set last month total and earnings
        $results[1]['type']             = 'last_month';
        $results[1]['total']            = $lastMonthCollection->getQuantity();
        $results[1]['earning']          = $lastMonthCollection->getRevenue();
        $results[1]['earning_currency'] = $this->formatCurrency($lastMonthCollection->getRevenue());
        //Set all earnings
        $results[2]['type']  = 'all_time';
        $results[2]['total'] = $all_sales_total;
        //Set bestsellers items
        $results[3]['type']  = 'bestsellers';
        $results[3]['items'] = $bestsellers;
        //Set top countries items
        $results[4]['type']  = 'countries';
        $results[4]['items'] = $topcountries;
        //Set top payments items
        $results[5]['type']  = 'payments';
        $results[5]['items'] = $toppayments;

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($results);
        $searchResult->setTotalCount(1);

        return $searchResult;
    }

    /**
     * @param \Lof\AdvancedReports\Model\ResourceModel\Earning\Collection $bestseller_collection
     * @return mixed
     */
    public function getBestsellerItems(\Lof\AdvancedReports\Model\ResourceModel\Earning\Collection $collection)
    {
        $items = [];
        if (0 < $collection->getSize()) {
            foreach ($collection as $item) {
                $tmp                   = [];
                $tmp['id']             = $item->getProductId();
                $tmp['name']           = $item->getProductName();
                $tmp['qty']            = (int) $item->getQtyOrdered();
                $tmp['total']          = $item->getBaseRowTotal();
                $tmp['total_currency'] = $this->formatCurrency($item->getBaseRowTotal());
                $items[]               = $tmp;
            }
        }

        return $items;
    }

    /**
     * @param \Lof\AdvancedReports\Model\ResourceModel\Earning\Collection $bestseller_collection
     * @return mixed
     */
    public function getTopcountriesItems(\Lof\AdvancedReports\Model\ResourceModel\Earning\Collection $collection)
    {
        $items = [];
        if (0 < $collection->getSize()) {
            foreach ($collection as $item) {
                $tmp                   = [];
                $tmp['country_id']     = $item->getCountryId();
                $tmp['country_name']   = $this->localeLists->getCountryTranslation($item->getCountryId());
                $tmp['total']          = $item->getTotalRevenueAmount();
                $tmp['total_currency'] = $this->formatCurrency($item->getTotalRevenueAmount());
                $items[]               = $tmp;
            }
        }

        return $items;
    }

    /**
     * @param \Lof\AdvancedReports\Model\ResourceModel\Earning\Collection $bestseller_collection
     * @return mixed
     */
    public function getTopPaymentItems(\Lof\AdvancedReports\Model\ResourceModel\Earning\Collection $collection)
    {
        $items = [];
        if (0 < $collection->getSize()) {
            foreach ($collection as $item) {
                $tmp                   = [];
                $tmp['payment_name']   = $item->getStore()->getConfig('payment/' . $item->getMethod() . '/title');
                $tmp['payment']        = $item->getMethod();
                $tmp['total']          = $item->getTotalRevenueAmount();
                $tmp['total_currency'] = $this->formatCurrency($item->getTotalRevenueAmount());
                $items[]               = $tmp;
            }
        }

        return $items;
    }
}
