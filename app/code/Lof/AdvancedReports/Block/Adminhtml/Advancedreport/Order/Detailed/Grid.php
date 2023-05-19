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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Order\Detailed;

class Grid extends \Lof\AdvancedReports\Block\Adminhtml\Grid\AbstractGrid {

    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = '';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';
    protected $_resource_grid_collection = null;
    protected $_scopeconfig;

    public function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
        $this->setFilterVisibility(true);
        $this->setPagerVisibility(true);
        $this->setId('detailedGrid');
        $this->setUseAjax(false);
        $this->setDefaultSort("created_at");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('order_filter');
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Order\Collection';
    }

    protected function _prepareColumns()
    {
        $filterData = $this->getFilterData();
        $this->addColumn('increment_id', [
            'header'          => __('Order #'),
            'index'           => 'increment_id',
            'width'           => '100px',
            'filter_data'     => $this->getFilterData(),
            'totals_label'    => __('Total'),
            'html_decorators' => ['nobr'],
        ]);

        $this->addColumn('status', [
            'header'  => __('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'width'   => '70px',
            'options' => $this->_objectManager->create('Magento\Sales\Model\Order\Config')->getStatuses(),
        ]);
        $payments = $this->_objectManager->create('Magento\Payment\Model\Config')->getActiveMethods();
        $methods  = [];

        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->_scopeConfig
                ->getValue('payment/' . $paymentCode . '/title');

            $methods[$paymentCode] = $paymentTitle;
        }

        $this->addColumn('method', [
            'header'       => __('Payment Method'),
            'index'        => 'method',
            'filter_index' => 'payment.method',
            'type'         => 'options',
            'width'        => '70px',
            'options'      => $methods,
        ]);

        $this->addColumn('customer_email', [
            'header' => __('Email'),
            'index'  => 'customer_email',
            'width'  => '70px',
        ]);

        $this->addColumn('customer_firstname', [
            'header'       => __('First Name'),
            'index'        => 'customer_firstname',
            'filter_index' => 'customer_firstname',
            'width'        => '70px',
        ]);

        $this->addColumn('customer_lastname', [
            'header'       => __('Last Name'),
            'index'        => 'customer_lastname',
            'filter_index' => 'customer_lastname',
            'width'        => '70px',
        ]);

        $this->addColumn('created_at', [
            'header' => __('Order Date'),
            'index'  => 'created_at',
            'type'   => 'datetime',
            'width'  => '100px',
            'filter' => false,
        ]);

        $this->addColumn('total_qty_ordered', [
            'header' => __('Qty. Ordered'),
            'index'  => 'total_qty_ordered',
            'type'   => 'number',
            'total'  => 'sum',
            'filter' => false,
        ]);

        $this->addColumn('total_qty_invoiced', [
            'header' => __('Qty. Invoiced'),
            'index'  => 'total_qty_invoiced',
            'type'   => 'number',
            'total'  => 'sum',
            'filter' => false,
        ]);

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $filterData = $this->getFilterData();

        $currencyCodeParam = $filterData->getData('currency_code') ?: null;
        $currencyCode      = $this->getCurrentCurrencyCode($currencyCodeParam);
        $rate              = $this->getRate($currencyCode) ?: 1;

        $this->addColumn('total_subtotal_amount', [
            'header'        => __('Total Amount'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_subtotal_amount',
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);

        $this->addColumn('total_invoiced_amount', [
            'header'        => __('Invoiced'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_invoiced_amount',
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);

        $this->addColumn('total_tax_amount_actual', [
            'header'        => __('Tax'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_tax_amount_actual',
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);

        $this->addColumn('total_discount_amount_actual', [
            'header'        => __('Discounts'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_discount_amount_actual',
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);

        $this->addColumn('total_shipping_amount_actual', [
            'header'        => __('Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_amount_actual',
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);

        $this->addColumn('total_cost_amount', [
            'header'        => __('Total Cost Amount'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_cost_amount',
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);

        $this->addColumn('total_income_amount', [
            'header'        => __('Income'),
            'index'         => 'total_income_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);

        // $this->addColumn('total_profit_amount', [
        //     'header'    => __('Total Profit'),
        //     'index'     => 'total_profit_amount',
        //     'type'          => 'currency',
        //     'currency_code' => $currencyCode,
        //     'total'     => 'sum',
        //     'rate'          => $rate,
        //     'filter'    => false,
        // ]);


        $this->addColumn('total_grossprofit_amount', [
            'header'        => __('Gross Profits'),
            'index'         => 'total_grossprofit_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);
        $this->addColumn('margin_profit', [
            'header'          => __('Profits Margin'),
            'index'           => 'margin_profit',
            'type'            => 'number',
            'filter'          => false,
            'renderer'        => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\Margin',
            'html_decorators' => ['nobr'],
        ]);

        $this->addColumn('total_net_profits', [
            'header'        => __('Net Profits'),
            'index'         => 'total_net_profits',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ]);


        $this->addExportType('*/*/exportOrderDetailedCsv', __('CSV'));
        $this->addExportType('*/*/exportOrderDetailedExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {

        $filterData  = $this->getFilterData();
        $report_type = $this->getReportType();
        $limit       = $filterData->getData("limit", null);
        if ( ! $limit) {
            $limit = $this->_defaultLimit;
        }
        $margin       = $this->getMarginProfit();
        $margin       = $margin ? (float) $margin : 0;
        $report_field = $filterData->getData("report_field", null);
        $report_field = $report_field ? $report_field : "main_table.created_at";
        $this->setCulumnDate($report_field);
        $currencyCode       = $this->getCurrentCurrencyCode(null);
        $storeIds           = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Order\Collection')
                                                   ->setOrderRate($currencyCode)
                                                   ->prepareOrderDetailedCollection($margin)
                                                   ->setDateColumnFilter($this->_columnDate)
                                                   ->addDateFromFilter($filterData->getData('filter_from', null))
                                                   ->addDateToFilter($filterData->getData('filter_to', null))
                                                   ->addStoreFilter($storeIds);

        $resourceCollection->join(['payment' => 'sales_order_payment'], 'main_table.entity_id=parent_id', 'method');

        $this->_addOrderStatusFilter($resourceCollection, $filterData);
        $this->_addCustomFilter($resourceCollection, $filterData);

        $resourceCollection->getSelect()
                           ->order(new \Zend_Db_Expr($this->getColumnOrder() . " " . $this->getColumnDir()));

        $resourceCollection->applyCustomFilter();

        $resourceCollection->setPageSize((int) $this->getParam($this->getVarNameLimit(), $limit));
        $resourceCollection->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));

        $order_filter = $this->getParam($this->getVarNameFilter(), null);

        $this->setCollection($resourceCollection);
        if ( ! $this->_registry->registry('report_collection')) {
            $this->_registry->register('report_collection', $resourceCollection);
        }
        $this->_prepareTotals('orders_count,total_subtotal_amount,total_qty_ordered,total_qty_invoiced,total_income_amount,total_revenue_amount,total_profit_amount,total_invoiced_amount,total_paid_amount,total_refunded_amount,total_tax_amount,total_tax_amount_actual,total_shipping_amount,total_shipping_amount_actual,total_discount_amount,total_discount_amount_actual,total_cost_amount,total_net_amount,total_grossprofit_amount,total_net_profits,total_gross_amount,total_amount,total_canceled_amount,margin_profit'); //Add this Line with all the columns you want to have in totals bar

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

    protected function _preparePage()
    {
        $this->getCollection()->setPageSize((int) $this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        $this->getCollection()->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/detailed', ['_current' => true]);
    }

}
