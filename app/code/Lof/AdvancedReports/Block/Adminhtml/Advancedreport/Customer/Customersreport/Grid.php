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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Customer\Customersreport;

class Grid extends \Lof\AdvancedReports\Block\Adminhtml\Grid\AbstractGrid
{

    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = 'period';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';
    protected $_resource_grid_collection = null;

    public function _construct()
    {
        parent::_construct();
        $this->setId('customersreportGrid');
        $this->setCountTotals(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(true);
        $this->setUseAjax(false);
    }

    protected function _prepareColumns()
    {
        $filterData = $this->getFilterData();

        $this->addColumn('customer_id', array(
            'header'    => __('ID'),
            'width'     => '50px',
            'index'     => 'customer_id',
            'type'  => 'number',
        ));

        $this->addColumn('customer_firstname', array(
            'header' =>  __('Firstname'),
            'index' => 'customer_firstname',
            'width' => '100px',
            'totals_label'  => __('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('customer_lastname', array(
            'header' =>   __('Lastname'),
            'index' => 'customer_lastname',
            'width' => '100px',
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('customer_email', array(
            'header' =>  __('Email'),
            'index' => 'customer_email',
            'width' => '100px',
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('city', array(
            'header' =>  __('Shipping City'),
            'index' => 'city',
            'width' => '100px',
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('postcode', array(
            'header' =>  __('Shipping Postcode'),
            'index' => 'postcode',
            'width' => '100px',
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('orders_count', array(
            'header'    =>  __('Number Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $filterData = $this->getFilterData();

        $currencyCodeParam = $filterData->getData( 'currency_code' ) ?: null;
        $currencyCode      = $this->getCurrentCurrencyCode( $currencyCodeParam );
        $rate              = $this->getRate( $currencyCode ) ?: 1;

        $this->addColumn('total_subtotal_amount', array(
            'header'    =>  __('Subtotal'),
            'index'     => 'total_subtotal_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_grandtotal_amount', array(
            'header'    =>  __('Grand Total'),
            'index'     => 'total_grandtotal_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_profit_amount', array(
            'header'    =>  __('Profit'),
            'index'     => 'total_profit_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_shipping_amount', array(
            'header'    =>  __('Shipping'),
            'index'     => 'total_shipping_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_tax_amount', array(
            'header'    =>  __('Tax'),
            'index'     => 'total_tax_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_discount_amount', array(
            'header'    =>  __('Discounts'),
            'index'     => 'total_discount_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_refunded_amount', array(
            'header'    =>  __('Refunded'),
            'index'     => 'total_refunded_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addExportType('*/*/exportCustomersreportCsv', __('CSV'));
        $this->addExportType('*/*/exportCustomersreportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        $report_type = $this->getReportType();
        $limit = $filterData->getData("limit", null);
        if(!$limit) {
            $limit = $this->_defaultLimit;
        }
        $report_field = $filterData->getData("report_field", null);
        $report_field = $report_field?$report_field: "main_table.created_at";
        $this->setCulumnDate($report_field);
        $this->setDefaultSort("orders_count");
        $this->setDefaultDir("ASC");

        $storeIds = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Customer\Collection')
            ->prepareCustomersreportCollection()
            ->setMainTableId("main_table.customer_id")
            ->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns());

        $this->_addOrderStatusFilter($resourceCollection, $filterData);
        $this->_addCustomFilter($resourceCollection, $filterData);

        if($customer_email = $filterData->getData('customer_email', null)){
            $resourceCollection->filterByCustomerEmail($customer_email);
        }
        $resourceCollection->getSelect()
                             ->order(new \Zend_Db_Expr($this->getColumnOrder()." ".$this->getColumnDir()));

        $resourceCollection->applyCustomFilter();

        $resourceCollection->setPageSize((int) $this->getParam($this->getVarNameLimit(), $limit));
        $resourceCollection->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));

        if ($this->getCountSubTotals()) {
            $this->getSubTotals();
        }

       if ($filterData->getData('show_empty_rows', false)) {
            $this->_reportsData->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData('year', null),
                $filterData->getData('month', null),
                $filterData->getData('day', null)
            );
        }
        $this->setCollection($resourceCollection);
        if(!$this->_registry->registry('report_collection')) {
            $this->_registry->register('report_collection', $resourceCollection);
        }



        $this->_prepareTotals('orders_count,total_subtotal_amount,total_grandtotal_amount,total_profit_amount,total_revenue_amount,total_profit_amount,total_invoiced_amount,total_paid_amount,total_refunded_amount,total_tax_amount,total_tax_amount_actual,total_shipping_amount,total_shipping_amount_actual,total_discount_amount,total_discount_amount_actual,total_canceled_amount'); //Add this Line with all the columns you want to have in totals bar
        return parent::_prepareCollection();
    }


    /**
     * Helper function to do after load modifications
     *
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/customersreport', array('_current'=>true));
    }

}
