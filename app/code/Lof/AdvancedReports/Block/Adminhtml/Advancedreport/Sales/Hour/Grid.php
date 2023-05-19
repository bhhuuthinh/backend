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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Sales\Hour;

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
        $this->setCountTotals( true );
        $this->setFilterVisibility( true );
        $this->setPagerVisibility( true );
        $this->setId( 'productsreportGrid' );
        $this->setUseAjax( false );
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Sales\Collection';
    }

    protected function _prepareColumns()
    {
        $filterData = $this->getFilterData();

        $this->addColumn( 'period', [
            'header'          => __( 'Period' ),
            'index'           => 'period',
            'width'           => 100,
            'filter_data'     => $this->getFilterData(),
            'totals_label'    => __( 'Total' ),
            'html_decorators' => [ 'nobr' ],
            'filter'          => false,
        ] );

        $this->addColumn( 'orders_count', [
            'header' => __( 'Number Of Orders' ),
            'index'  => 'orders_count',
            'type'   => 'number',
            'total'  => 'sum',
            'filter' => false,
        ] );

        $this->addColumn( 'total_qty_ordered', [
            'header' => __( 'Items Ordered' ),
            'index'  => 'total_qty_ordered',
            'type'   => 'number',
            'total'  => 'sum',
            'filter' => false,
        ] );

        if ( $this->getFilterData()->getStoreIds() ) {
            $this->setStoreIds( explode( ',', $this->getFilterData()->getStoreIds() ) );
        }
        $filterData = $this->getFilterData();

        $currencyCodeParam = $filterData->getData( 'currency_code' ) ?: null;
        $currencyCode      = $this->getCurrentCurrencyCode( $currencyCodeParam );
        $rate              = $this->getRate( $currencyCode ) ?: 1;

        $this->addColumn( 'total_revenue_amount', [
            'header'        => __( 'Revenue' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_revenue_amount',
            'total'         => 'sum',
            'rate'          => $rate,
            'filter'        => false,
        ] );
        $this->addExportType( '*/*/exportSalesByHourCsv', __( 'CSV' ) );
        $this->addExportType( '*/*/exportSalesByHourExcel', __( 'Excel XML' ) );
        // }

        // $this->initExportActions();

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        $filterData  = $this->getFilterData();
        $report_type = $this->getReportType();
        $limit       = $filterData->getData( "limit", null );
        if ( ! $limit ) {
            $limit = $this->_defaultLimit;
        }
        $report_field = $filterData->getData( "report_field", null );
        $report_field = $report_field ? $report_field : "main_table.created_at";
        $this->setCulumnDate( $report_field );
        $this->setDefaultSort( "period" );
        $this->setDefaultDir( "DESC" );
        $currencyCode       = $this->getCurrentCurrencyCode( null );
        $storeIds           = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create( $this->getResourceCollectionName() )
                                                   ->setOrderRate( $currencyCode )
                                                   ->prepareHourlyCollection()
                                                   ->setPeriodType( "hour" )
                                                   ->setDateColumnFilter( $this->_columnDate )
                                                   ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                   ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                   ->addStoreFilter( $storeIds )
                                                   ->setAggregatedColumns( $this->_getAggregatedColumns() );


        $this->_addOrderStatusFilter( $resourceCollection, $filterData );
        $this->_addCustomFilter( $resourceCollection, $filterData );
        $resourceCollection->getSelect()
                           ->order( new \Zend_Db_Expr( $this->getColumnOrder() . " " . $this->getColumnDir() ) );
        $resourceCollection->applyCustomFilter();

        $resourceCollection->setPageSize( (int) $this->getParam( $this->getVarNameLimit(), $limit ) );
        $resourceCollection->setCurPage( (int) $this->getParam( $this->getVarNamePage(), $this->_defaultPage ) );


        if ( $this->getCountSubTotals() ) {
            $this->getSubTotals();
        }

        if ( ! $this->getTotals() ) {
            $totalsCollection = $this->_objectManager->create( $this->getResourceCollectionName() )
                                                     ->setOrderRate( $currencyCode )
                                                     ->prepareHourlyCollection()
                                                     ->setDateColumnFilter( $this->_columnDate )
                                                     ->setPeriodType( "hour" )
                                                     ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                     ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                     ->addStoreFilter( $storeIds )
                                                     ->setAggregatedColumns( $this->_getAggregatedColumns() )
                                                     ->isTotals( true );

            $this->_addOrderStatusFilter( $totalsCollection, $filterData );
            $this->_addCustomFilter( $totalsCollection, $filterData );

            $totalsCollection->getSelect()
                             ->order( new \Zend_Db_Expr( $this->getColumnOrder() . " " . $this->getColumnDir() ) );

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

        $this->_prepareTotals( 'orders_count,total_qty_ordered,total_qty_invoiced,total_income_amount,total_revenue_amount,total_profit_amount,total_invoiced_amount,total_paid_amount,total_refunded_amount,total_tax_amount,total_tax_amount_actual,total_shipping_amount,total_shipping_amount_actual,total_discount_amount,total_discount_amount_actual,total_canceled_amount,total_subtotal_amount,total_grandtotal_amount' );

        return parent::_prepareCollection();
    }
}
