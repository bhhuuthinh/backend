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

class Bestsellers extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Earning\AbstractEarning {
    public function _beforeToHtml()
    {
        $filterData   = $this->getFilterData();
        $currencyCode = $this->getCurrentCurrencyCode(null);
        $store_ids    = $filterData->getData('store_ids', 0);
        $collection   = $this->_objectManager->create($this->getResourceCollectionName())
                                             ->setOrderRate($currencyCode)
                                             ->prepareBestsellersCollection()
                                             ->setDateColumnFilter($this->_columnDate)
                                             ->addYearFilter($filterData->getData('filter_year', null))
                                             ->addMonthFilter($filterData->getData('filter_month', null))
                                             ->addDayFilter($filterData->getData('filter_day', null));

        if ($store_ids) {
            $collection->addStoreFilter($store_ids);
        }

        $collection->join(['order_item' => 'sales_order_item'], 'main_table.entity_id=order_id', ['product_id', 'order_item.name', 'order_item.price']);
        $collection->addOrderStatusFilter($filterData->getData('order_statuses'));
        $collection->applyCustomFilter();
        $collection->getSelect()
                   ->group('product_id')
                   ->order(new \Zend_Db_Expr('qty_ordered DESC'))
                   ->limit($this->_limit);
        $this->setReportCollection($collection);

        return parent::_beforeToHtml();
    }

    public function getBestsellerReportLink()
    {
        return $this->getUrl("*/advancedreports_products/productsreport");
    }
}
