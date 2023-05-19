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
use Lof\AdvancedReports\Api\EarningInterface;
use Lof\AdvancedReports\Model\AbstractReport;
use Magento\Framework\Api\SortOrder;

class EarningRepository extends AbstractReport implements EarningInterface
{
    protected $_limit = 10;
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';

    public function __construct(
        \Lof\AdvancedReports\Helper\Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Lof\AdvancedReports\Helper\Api\Datefield $helperDatefield,
        \Lof\AdvancedReports\Api\Data\ReportdataInterfaceFactory $searchResultsFactory
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
        return 'Lof\AdvancedReports\Model\ResourceModel\Earning\Collection';
    }

    /**
    * @return \Magento\Framework\DataObject
    */
    public function initFilterData($filter_field = []) {
        $requestData = [];
        $lofFilter = isset($filter_field['lofFilter'])?isset($filter_field['lofFilter']):null;
        $storeIds = isset($filter_field['storeIds'])?isset($filter_field['storeIds']):null;
        $isCurrent = isset($filter_field['isCurrent'])?$filter_field['isCurrent']:0;

        if($lofFilter) {
            $requestData = $this->_objectManager->get(
                'Magento\Backend\Helper\Data'
            )->prepareFilterString(
                $lofFilter
            );
        }

        $requestData['store_ids'] = $storeIds;
        if(!isset($requestData['filter_year']) || !$requestData['filter_year']) {
          $requestData['filter_year'] = isset($filter_field['filter_year'])?$filter_field['filter_year']:"";
        }
        if(!isset($requestData['filter_month']) || !$requestData['filter_month']) {
          $requestData['filter_month'] = isset($filter_field['filter_month'])?$filter_field['filter_month']:"";
        }
        if(!isset($requestData['filter_day']) || !$requestData['filter_day']) {
          $requestData['filter_day'] = isset($filter_field['filter_day'])?$filter_field['filter_day']:"";
        }
        if(!isset($requestData['show_order_statuses']) || ($requestData['show_order_statuses'] == NULL && $requestData['show_order_statuses'] == "")) {
          $requestData['show_order_statuses'] = isset($filter_field['show_order_statuses'])?(int)$filter_field['show_order_statuses']:1;;
        }

        if($isCurrent) {
            $requestData['filter_year'] = date("Y");
            $requestData['filter_month'] = date("m");
            $requestData['filter_day'] = '';
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
     * @return \Lof\AdvancedReports\Api\Data\ReportdataInterface
     */
    public function getEarning(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {

        $filter_fields = [
                        "show_order_statuses"=>1,
                        "filter_year"=>0,
                        "filter_month"=>0,
                        "filter_day"=>0,
                        "order_statuses"=>"complete",
                        "isCurrent"=>0,
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
            ->prepareReportCollection()
            ->setDateColumnFilter($this->_columnDate)
            ->addYearFilter($filterData->getData('filter_year', null))
            ->addMonthFilter($filterData->getData('filter_month', null))
            ->addDayFilter($filterData->getData('filter_day', null))
            ->addStoreFilter($store_ids);

        $this->_addOrderStatusFilter($resourceCollection, $filterData);

        $resourceCollection->getSelect()
                            ->group($this->_columnGroupBy);
                            //->order(new \Zend_Db_Expr($sort." ".$dir));

        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $resourceCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
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
        //init \Lof\AdvancedReports\Api\Data\ReportdataInterface
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($resourceCollection->getItems());
        $searchResult->setTotalCount($resourceCollection->getSize());
        return $searchResult;
    }

    protected function _getPeriodType () {
        $period_type = "day";
        $filterData = $this->getFilterData();
        $filter_year = $filterData->getData('filter_year', null);
        $filter_month = $filterData->getData('filter_month', null);
        $filter_day = $filterData->getData('filter_day', null);

        if($filter_year && $filter_month && $filter_day) {
          $period_type = "hour";
        }elseif($filter_year && $filter_month && !$filter_day) {
          $period_type = "day";
        }elseif($filter_year && !$filter_month && !$filter_day) {
          $period_type = "month";
        }elseif(!$filter_year && !$filter_month && !$filter_day) {
          $period_type = "year";
        }
        return $period_type;
    }

    protected function _convertGridData(&$collection = null) {

        if($collection && $collection->getSize()) {
            $period_type = $this->_getPeriodType();
            $filterData = $this->getFilterData();
            $filter_year = $filterData->getData('filter_year', null);
            $filter_month = $filterData->getData('filter_month', null);
            $filter_day = $filterData->getData('filter_day', null);
            foreach($collection as &$item) {
                //convert period field
                $period_label = $this->_helperDatefield->renderPeriodField($item->getPeriod(), $period_type, $filter_year, $filter_month, $filter_day);
                $item->setData("period_label", $period_label);
                $item->setPeriodLabel($period_label);

                $revenue_currency = $this->formatCurrency($item->getTotalRevenueAmount());
                $item->setData("revenue_currency", $revenue_currency);
                $item->setRevenueCurrency($revenue_currency);
            }
        }
        return $collection;
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
}
