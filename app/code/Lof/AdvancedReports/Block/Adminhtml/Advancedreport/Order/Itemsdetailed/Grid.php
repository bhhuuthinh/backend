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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Order\Itemsdetailed;

class Grid extends \Lof\AdvancedReports\Block\Adminhtml\Grid\AbstractGrid
{

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
        $this->setId('itemsdetailedGrid');
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
        $this->addColumn('increment_id',[
            'header' => __('Order #'),
            'index' => 'increment_id',
            'width' => '100px',
            'filter_data'   => $this->getFilterData(),
            'totals_label'  => __('Total'),
            'html_decorators' => array('nobr'),
        ]);

        $this->addColumn('status', [
            'header' => __('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => $this->_objectManager->create('Magento\Sales\Model\Order\Config')->getStatuses(),
        ]);
        $payments  = $this->_objectManager->create('Magento\Payment\Model\Config')->getActiveMethods();
        $methods = array();

        foreach ($payments as $paymentCode=>$paymentModel)
        {
            $paymentTitle = $this->_scopeConfig
                ->getValue('payment/'.$paymentCode.'/title');

            $methods[$paymentCode] = $paymentTitle;
        }

        $this->addColumn('method', [
            'header' => __('Payment Method'),
            'index' => 'method',
            'filter_index' => 'payment.method',
            'type'  => 'options',
            'width' => '70px',
            'options' => $methods,
        ]);

        $this->addColumn('created_at', [
            'header' => __('Order Date'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter'    => false,
        ]);

        $this->addColumn('sku', array(
            'header' =>  __('SKU'),
            'index' => 'sku',
            'width' => '100px',
            'html_decorators' => array('nobr')
        ));

        $this->addColumn('name', array(
            'header'    =>  __('Product Name'),
            'width'     => '215px',
            'index'     => 'name',
            'type'      => 'text',
            'filter'    => false,
            'filter_data'   => $this->getFilterData(),
            'renderer'      => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\Productname',
            'html_decorators' => array('nobr')
        ));

        $this->addColumn('real_qty_ordered', array(
            'header'    =>  __('Qty. Ordered'),
            'index'     => 'real_qty_ordered',
            'type'      => 'number',
            'filter'    => false,
            'total'     => 'sum',
        ));

        $this->addColumn('qty_invoiced', array(
            'header'    =>  __('Qty. Invoiced'),
            'index'     => 'qty_invoiced',
            'type'      => 'number',
            'filter'    => false,
            'total'     => 'sum',
        ));

        $this->addColumn('real_qty_shipped', array(
            'header'    =>  __('Qty. Shipped'),
            'index'     => 'real_qty_shipped',
            'type'      => 'number',
            'filter'    => false,
            'total'     => 'sum',
        ));

        $this->addColumn('real_qty_refunded', array(
            'header'    => __('Qty. Refunded'),
            'index'     => 'real_qty_refunded',
            'type'      => 'number',
            'filter'    => false,
            'total'     => 'sum',
        ));


        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $filterData = $this->getFilterData();

        $currencyCodeParam = $filterData->getData( 'currency_code' ) ?: null;
        $currencyCode      = $this->getCurrentCurrencyCode( $currencyCodeParam );
        $rate              = $this->getRate( $currencyCode ) ?: 1;

        $this->addColumn('price', array(
            'header'    => __('Price'),
            'align'     => 'right',
            'filter'    => false,
            'currency_code' => $currencyCode,
            'index'     => 'price',
            'type'      => 'currency',
            'rate'          => $rate,
            'total'     => 'sum',
        ));

        $this->addColumn('base_price', array(
            'header'    =>  __('Original Price'),
            'align'     => 'right',
            'filter'    => false,
            'currency_code' => $currencyCode,
            'index'     => 'price',
            'type'      => 'currency',
            'rate'          => $rate,
            'total'     => 'sum',
        ));

        $this->addColumn('subtotal', array(
            'header'            => __('Subtotal'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'subtotal',
            'filter'    => false,
            'total'             => 'sum',
            'rate'              => $rate,
        ));

        $this->addColumn('discount_amount', array(
            'header'            => __('Discounts'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'filter'    => false,
            'index'             => 'discount_amount',
            'total'             => 'sum',
            'rate'              => $rate,
        ));

        $this->addColumn('tax_amount', array(
            'header'            => __('Tax'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'tax_amount',
            'filter'    => false,
            'total'             => 'sum',
            'rate'              => $rate,
        ));

        $this->addColumn('row_total', array(
            'header'            => __('Total'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'filter'    => false,
            'index'             => 'row_total',
            'total'             => 'sum',
            'rate'              => $rate,
        ));

        $this->addColumn('row_total_incl_tax', array(
            'header'    => __('Total Incl. Tax'),
            'index'     => 'row_total_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'filter'    => false,
            'rate'          => $rate,
        ));

        $this->addColumn('row_invoiced', array(
            'header'            => __('Invoiced'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'row_invoiced',
            'total'             => 'sum',
            'filter'    => false,
            'rate'              => $rate,
        ));

        $this->addColumn('tax_invoiced', array(
            'header'            => __('Tax Invoiced'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'tax_invoiced',
            'filter'    => false,
            'total'             => 'sum',
            'rate'              => $rate,
        ));

        $this->addColumn('row_invoiced_incl_tax', array(
            'header'    => __('Invoiced Incl. Tax'),
            'index'     => 'row_invoiced_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'filter'    => false,
            'rate'          => $rate,
        ));

        $this->addColumn('amount_refunded', array(
            'header'            => __('Refunded'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'filter'    => false,
            'index'             => 'amount_refunded',
            'total'             => 'sum',
            'rate'              => $rate,
        ));

        $this->addColumn('real_tax_refunded', array(
            'header'            => __('Tax Refunded'),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'real_tax_refunded',
            'filter'    => false,
            'total'             => 'sum',
            'rate'              => $rate,
        ));

        $this->addColumn('row_refunded_incl_tax', array(
            'header'    => __('Refunded Incl. Tax'),
            'index'     => 'row_refunded_incl_tax',
            'type'          => 'currency',
            'filter'    => false,
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_cost_amount', array(
            'header'    => __('Total Cost'),
            'index'     => 'total_cost_amount',
            'type'          => 'currency',
            'filter'    => false,
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_revenue_amount_excl_tax', array(
            'header'    => __('Total Revenue (excl.tax)'),
            'index'     => 'total_revenue_amount_excl_tax',
            'type'          => 'currency',
            'filter'    => false,
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_revenue_amount', array(
            'header'    =>  __('Total Revenue'),
            'index'     => 'total_revenue_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'filter'    => false,
            'rate'          => $rate,
        ));

        $this->addColumn('total_profit_amount', array(
            'header'    =>  __('Total Profit'),
            'index'     => 'total_profit_amount',
            'type'          => 'currency',
            'filter'    => false,
            'currency_code' => $currencyCode,
            'total'     => 'sum',
            'rate'          => $rate,
        ));

        $this->addColumn('total_margin', array(
            'header'    =>  __('Total Margin'),
            'index'     => 'total_margin',
            'type'      => 'number',
            'filter'    => false,
            'renderer'      => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\Margin',
            'html_decorators' => array('nobr')
        ));

        $this->addColumn('action',
            [
                'header'    =>  __('Order Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getOrderId',
                'actions'   => array(
                    array(
                        'caption' =>  __('View'),
                        'url'     => array('base'=>'sales/order/view'),
                        'field'   => 'order_id',
                        'data-column' => 'action',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ]);

        $this->addColumn('view_product_action',
            array(
                'header'    => __('View Product'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getProductId',
                'actions'   => array(
                    array(
                        'caption' => __('View'),
                        'url'     => array(
                            'base'=>'catalog/product/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
            ));
        $this->addExportType('*/*/exportOrderItemsDetailedCsv', __('CSV'));
        $this->addExportType('*/*/exportOrderItemsDetailedExcel', __('Excel XML'));
        // }

        // $this->initExportActions();

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
        $this->setDefaultSort("main_table.created_at");
        $this->setDefaultDir("DESC");
        $currencyCode       = $this->getCurrentCurrencyCode(null);
        $storeIds = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Order\Collection')
            ->setOrderRate($currencyCode)
            ->prepareOrderItemDetailedCollection()
            ->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($storeIds);

        $resourceCollection->join(array('payment'=>'sales_order_payment'),'main_table.entity_id=parent_id','method');

        $this->_addOrderStatusFilter($resourceCollection, $filterData);
        $this->_addCustomFilter($resourceCollection, $filterData);

        $resourceCollection->getSelect()
            ->order(new \Zend_Db_Expr($this->getColumnOrder()." ".$this->getColumnDir()));
        $resourceCollection->applyCustomFilter();

        // echo $resourceCollection->getSelect();

        $resourceCollection->setPageSize((int) $this->getParam($this->getVarNameLimit(), $limit));
        $resourceCollection->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));

        // $order_filter = $this->getParam($this->getVarNameFilter(), null);


        $this->setCollection($resourceCollection);
        if(!$this->_registry->registry('report_collection')) {
            $this->_registry->register('report_collection', $resourceCollection);
        }

        $this->_prepareTotals('real_qty_ordered,qty_invoiced,real_qty_shipped,real_qty_refunded,price,base_price,subtotal,discount_amount,tax_amount,row_total,row_total_incl_tax,row_invoiced,row_invoiced_incl_tax,amount_refunded,real_tax_refunded,row_refunded_incl_tax,total_cost_amount,total_revenue_amount_excl_tax,total_revenue_amount,total_profit_amount'); //Add this Line with all the columns you want to have in totals bar

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
        return $this->getUrl('*/*/itemsdetailed', array('_current'=>true));
    }

}

