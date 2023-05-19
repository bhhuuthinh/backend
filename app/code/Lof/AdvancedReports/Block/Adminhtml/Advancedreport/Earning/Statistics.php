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

class  Statistics extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Earning\AbstractEarning
{
    protected $_lifetime_sales_total;
    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = 'period';
    protected $_columnOrder = 'period';
    protected $_orderDir = 'ASC';

    // public function __construct()
    // {
    //     parent::__construct();
    //     if($this->hasData("template") && $template = $this->getData("template")) {
    //         $this->setTemplate($template);
    //     } else {
    //         $this->setTemplate('report/earning/statistics.phtml');
    //     }

    // }

    public function _beforeToHtml()
    {
    	$filterData = $this->getFilterData();
    	$store_ids = $filterData->getData('store_ids', 0);

    	/*Load lifetime order totals*/
        $collection = $this->_objectManager->create('Magento\Reports\Model\ResourceModel\Order\Collection')
            ->calculateSales($store_ids);

        if($store_ids) {
        	$collection->addFieldToFilter('store_id', $store_ids);
        }

        $collection->load();
        $sales = $collection->getFirstItem();

        $this->_lifetime_sales_total = $sales->getLifetime();
        /*End Load lifetime order totals*/

        /*Load current month totals and last month totals*/
        $current_month = date("m");
		$current_month_name = date('F', mktime(0, 0, 0, $current_month , 10));
        $current_year = date("Y");
        $this->setCurrentMonth($current_month_name);

    	return parent::_beforeToHtml();
    }

    public function getCurrentTotals() {
    	return $this->_lifetime_sales_total;
    }

    public function getTotals()
    {
        return $this->_lifetime_sales_total;
    }
}
