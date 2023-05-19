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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Sales\Paymenttype;

use Magento\Payment\Model\MethodInterface;
use Magento\Store\Model\ScopeInterface;

class Grid extends \Lof\AdvancedReports\Block\Adminhtml\Grid\AbstractGrid {

    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = 'period';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';
    protected $_resource_grid_collection = null;
    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Sales\Collection';
    }

    protected function _prepareColumns()
    {
        $filterData = $this->getFilterData();

//        $payments = $this->_objectManager->create( 'Magento\Payment\Model\Config' )->getActiveMethods();
        $payments = $this->getPaymentMethods();
        $methods   = [];
        foreach ( $payments as $paymentCode => $paymentModel ) {
            $paymentTitle            = $this->_storeManager->getStore()->getConfig( 'payment/' . $paymentCode . '/title' );
            $methods[ $paymentCode ] = $paymentTitle;
        }


        $this->addColumn( 'method', [
            'header'          => __( 'Payment Method' ),
            'index'           => 'method',
            'filter_index'    => 'payment.method',
            'type'            => 'options',
            'width'           => '100px',
            'options'         => $methods,
            'totals_label'    => __( 'Total' ),
            'html_decorators' => [ 'nobr' ],
        ] );

        $this->addColumn( 'orders_count', [
            'header' => __( 'Orders' ),
            'index'  => 'orders_count',
            'type'   => 'number',
            'total'  => 'sum',
        ] );

        $this->addColumn( 'total_qty_ordered', [
            'header' => __( 'Sales Items' ),
            'index'  => 'total_qty_ordered',
            'type'   => 'number',
            'total'  => 'sum',
        ] );

        $this->addColumn( 'total_qty_invoiced', [
            'header'            => __( 'Items' ),
            'index'             => 'total_qty_invoiced',
            'type'              => 'number',
            'total'             => 'sum',
            'visibility_filter' => [ 'show_actual_columns' ],
        ] );

        if ( $this->getFilterData()->getStoreIds() ) {
            $this->setStoreIds( explode( ',', $this->getFilterData()->getStoreIds() ) );
        }
        $filterData = $this->getFilterData();

        $currencyCodeParam = $filterData->getData( 'currency_code' ) ?: null;
        $currencyCode      = $this->getCurrentCurrencyCode( $currencyCodeParam );
        $rate              = $this->getRate( $currencyCode ) ?: 1;

        $this->addColumn( 'total_income_amount', [
            'header'        => __( 'Sales Total' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_income_amount',
            'total'         => 'sum',
            'rate'          => $rate,
        ] );

        $this->addColumn( 'total_revenue_amount', [
            'header'            => __( 'Revenue' ),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_revenue_amount',
            'total'             => 'sum',
            'visibility_filter' => [ 'show_actual_columns' ],
            'rate'              => $rate,
        ] );

        $this->addColumn( 'total_profit_amount', [
            'header'            => __( 'Profit' ),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_profit_amount',
            'total'             => 'sum',
            'visibility_filter' => [ 'show_actual_columns' ],
            'rate'              => $rate,
        ] );
        $this->addColumn( 'total_invoiced_amount', [
            'header'        => __( 'Invoiced' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_invoiced_amount',
            'total'         => 'sum',
            'rate'          => $rate,
        ] );
        $this->addColumn( 'total_paid_amount', [
            'header'            => __( 'Paid' ),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_paid_amount',
            'total'             => 'sum',
            'visibility_filter' => [ 'show_actual_columns' ],
            'rate'              => $rate,
        ] );
        $this->addColumn( 'total_refunded_amount', [
            'header'        => __( 'Refunded' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_refunded_amount',
            'total'         => 'sum',
            'rate'          => $rate,
        ] );
        $this->addColumn( 'total_tax_amount', [
            'header'        => __( 'Sales Tax' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_tax_amount',
            'total'         => 'sum',
            'rate'          => $rate,
        ] );
        $this->addColumn( 'total_tax_amount_actual', [
            'header'            => __( 'Tax' ),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_tax_amount_actual',
            'total'             => 'sum',
            'visibility_filter' => [ 'show_actual_columns' ],
            'rate'              => $rate,
        ] );
        $this->addColumn( 'total_shipping_amount', [
            'header'        => __( 'Sales Shipping' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_amount',
            'total'         => 'sum',
            'rate'          => $rate,
        ] );
        $this->addColumn( 'total_shipping_amount_actual', [
            'header'            => __( 'Shipping' ),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_shipping_amount_actual',
            'total'             => 'sum',
            'visibility_filter' => [ 'show_actual_columns' ],
            'rate'              => $rate,
        ] );
        $this->addColumn( 'total_discount_amount', [
            'header'        => __( 'Sales Discount' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_discount_amount',
            'total'         => 'sum',
            'rate'          => $rate,
        ] );
        $this->addColumn( 'total_discount_amount_actual', [
            'header'            => __( 'Discount' ),
            'type'              => 'currency',
            'currency_code'     => $currencyCode,
            'index'             => 'total_discount_amount_actual',
            'total'             => 'sum',
            'visibility_filter' => [ 'show_actual_columns' ],
            'rate'              => $rate,
        ] );
        $this->addColumn( 'total_canceled_amount', [
            'header'        => __( 'Canceled' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_canceled_amount',
            'total'         => 'sum',
            'rate'          => $rate,
        ] );
        $this->addExportType( '*/*/exportSalesByPaymentTypeCsv', __( 'CSV' ) );
        $this->addExportType( '*/*/exportSalesByPaymentTypeExcel', __( 'Excel XML' ) );

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        $filterData  = $this->getFilterData();
        $report_type = $this->getReportType();
        $report_field = $filterData->getData( "report_field", null );
        $report_field = $report_field ? $report_field : "main_table.created_at";
        $this->setCulumnDate( $report_field );
        $this->setDefaultSort( "orders_count" );
        $this->setDefaultDir( "DESC" );
        $order = $this->getColumnOrder();
        if ( "month" == $this->getPeriodType() ) {
            $order = "main_table.created_at";
        }
        $currencyCode       = $this->getCurrentCurrencyCode( null );
        $storeIds           = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create( $this->getResourceCollectionName() )
            ->setOrderRate( $currencyCode )
            ->preparePaymentReportCollection()
            ->setDateColumnFilter( $this->_columnDate )
            ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
            ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
            ->addStoreFilter( $storeIds )
            ->setAggregatedColumns( $this->_getAggregatedColumns() );


        $this->_addOrderStatusFilter( $resourceCollection, $filterData );
        $this->_addCustomFilter( $resourceCollection, $filterData );
        $resourceCollection->getSelect()
            ->group( 'method' )
            ->order( new \Zend_Db_Expr( $order . " " . $this->getColumnDir() ) );
        $resourceCollection->applyCustomFilter();

        if ( $this->getCountSubTotals() ) {
            $this->getSubTotals();
        }

        if ( ! $this->getTotals() ) {
            $totalsCollection = $this->_objectManager->create( $this->getResourceCollectionName() )
                ->setOrderRate( $currencyCode )
                ->preparePaymentReportCollection()
                ->setDateColumnFilter( $this->_columnDate )
                ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                ->addStoreFilter( $storeIds )
                ->setAggregatedColumns( $this->_getAggregatedColumns() )
                ->isTotals( true );

            $this->_addOrderStatusFilter( $totalsCollection, $filterData );
            $this->_addCustomFilter( $totalsCollection, $filterData );

            $totalsCollection->getSelect()
                ->group( 'method' )
                ->order( new \Zend_Db_Expr( $order . " " . $this->getColumnDir() ) );

            $totalsCollection->applyCustomFilter();

            foreach ( $totalsCollection as $item ) {
                $this->setTotals( $item );
                break;
            }
        }
        $this->setCollection( $resourceCollection );

        if ( ! $this->_registry->registry( 'report_collection' ) ) {
            $this->_registry->register( 'report_collection', $resourceCollection );
        }

        $this->_prepareTotals( 'orders_count,total_qty_ordered,total_qty_invoiced,total_income_amount,total_revenue_amount,total_profit_amount,total_invoiced_amount,total_paid_amount,total_refunded_amount,total_tax_amount,total_tax_amount_actual,total_shipping_amount,total_shipping_amount_actual,total_discount_amount,total_discount_amount_actual,total_canceled_amount' );

        return parent::_prepareCollection();
    }

    public function getPaymentMethods()
    {
        $methods = [];
        foreach ($this->_scopeConfig->getValue('payment', ScopeInterface::SCOPE_STORE, null) as $code => $data) {
            if (isset($data['model'])) {
                /** @var MethodInterface $methodModel Actually it's wrong interface */
                $methodModel = $this->_paymentMethodFactory->create($data['model']);
                $methodModel->setStore(null);
                $methods[$code] = $methodModel;
            }
        }
        return $methods;
    }
}
