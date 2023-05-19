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
class  Topcountries extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Earning\AbstractEarning
{ 
    public function _beforeToHtml()
    {
    	$filterData = $this->getFilterData();
    	$store_ids = $filterData->getData('store_ids', 0);
    	$collection =  $this->_objectManager->create($this->getResourceCollectionName())
            ->prepareReportCollection()
            ->setDateColumnFilter($this->_columnDate)
            ->addYearFilter($filterData->getData('filter_year', null))
            ->addMonthFilter($filterData->getData('filter_month', null))
            ->addDayFilter($filterData->getData('filter_day', null));

        if($store_ids) {
            $collection->addStoreFilter($store_ids);
        }

        $collection->join(array('address'=>'sales_order_address'),'main_table.entity_id=parent_id','country_id');
        $collection->addOrderStatusFilter($filterData->getData('order_statuses')); 
        $collection->applyCustomFilter();
        $collection->getSelect()
                            ->group('address.country_id')
                            ->order(new \Zend_Db_Expr('total_revenue_amount DESC'))
                            ->limit($this->_limit);            
        $this->setReportCollection($collection);
        // echo $collection->getSelect();
    	return parent::_beforeToHtml();
    }
    public function getCountriesReportLink() {
        return $this->getUrl("*/advancedreports_sales/country");
    }
}