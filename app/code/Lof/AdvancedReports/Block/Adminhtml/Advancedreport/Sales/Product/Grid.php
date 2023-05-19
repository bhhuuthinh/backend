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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Sales\Product;

class Grid extends \Lof\AdvancedReports\Block\Adminhtml\Grid\AbstractGrid {

    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = '';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';
    protected $_resource_grid_collection = null;

    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Products\Collection';
    }

    protected function _prepareColumns()
    {
        $filterData = $this->getFilterData();
        $this->addColumn( 'period', [
            'header'          => __( 'Period' ),
            'index'           => 'period',
            'width'           => 100,
            'filter_data'     => $this->getFilterData(),
            'period_type'     => $this->getPeriodType(),
            'renderer'        => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\Dateperiod',
            'totals_label'    => __( 'Total' ),
            'html_decorators' => [ 'nobr' ],
            'filter'          => false,
        ] );

        $this->addColumn( 'orders_count', [
            'header' => __( 'Sales Count' ),
            'index'  => 'orders_count',
            'type'   => 'number',
            'total'  => 'sum',
        ] );

        $this->addColumn( 'total_qty', [
            'header' => __( 'Qty Ordered' ),
            'index'  => 'total_qty',
            'type'   => 'number',
            'total'  => 'sum',
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
        ] );

        $this->addExportType( '*/*/exportSalesByProductCsv', __( 'CSV' ) );
        $this->addExportType( '*/*/exportSalesByProductExcel', __( 'Excel XML' ) );

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        $filterData  = $this->getFilterData();
        $report_type = $this->getReportType();
        $limit       = $filterData->getData( "limit", null );
        if ( ! $filterData->getData( 'product_sku', null ) || ! $filterData->getData( 'filter_from', null ) || ! $filterData->getData( 'filter_to', null ) ) {
            $this->setCountTotals( false );
            $this->setCountSubTotals( false );

            return parent::_prepareCollection();
        }
        $report_field = $filterData->getData( "report_field", null );
        $report_field = $report_field ? $report_field : "main_table.created_at";
        $this->setCulumnDate( $report_field );
        $this->setDefaultSort( "period" );
        $this->setDefaultDir( "DESC" );
        $order = $this->getColumnOrder();
        if ( "month" == $this->getPeriodType() ) {
            $order = "main_table.created_at";
        }

        $currencyCode       = $this->getCurrentCurrencyCode( null );
        $storeIds           = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create( $this->getResourceCollectionName() )
                                                   ->setOrderRate( $currencyCode )
                                                   ->prepareProductReportCollection()
                                                   ->setMainTableId( "product_id" )
                                                   ->setPeriodType( $this->getPeriodType() )
                                                   ->setDateColumnFilter( $this->_columnDate )
                                                   ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                   ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                   ->addProductIdFilter( $filterData->getData( 'product_id', null ) )
                                                   ->addProductSkuFilter( $filterData->getData( 'product_sku', null ) )
                                                   ->addStoreFilter( $storeIds );

        $this->_addOrderStatusFilter( $resourceCollection, $filterData );
        $this->_addCustomFilter( $resourceCollection, $filterData );
        $resourceCollection->getSelect()
                           ->order( new \Zend_Db_Expr( $order . " " . $this->getColumnDir() ) );
        $resourceCollection->applyCustomFilter();

        if ( $filterData->getData( 'show_empty_rows', false ) ) {
            $this->_reportsData->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData( 'year', null ),
                $filterData->getData( 'month', null ),
                $filterData->getData( 'day', null )
            );
        }

        if ( $this->getCountSubTotals() ) {
            $this->getSubTotals();
        }

        if ( ! $this->getTotals() ) {
            $totalsCollection = $this->_objectManager->create( $this->getResourceCollectionName() )
                                                     ->setOrderRate( $currencyCode )
                                                     ->prepareProductReportCollection()
                                                     ->setMainTableId( "product_id" )
                                                     ->setDateColumnFilter( $this->_columnDate )
                                                     ->setPeriodType( $this->getPeriodType() )
                                                     ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                     ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                     ->addProductIdFilter( $filterData->getData( 'product_id', null ) )
                                                     ->addProductSkuFilter( $filterData->getData( 'product_sku', null ) )
                                                     ->addStoreFilter( $storeIds )
                                                     ->setAggregatedColumns( $this->_getAggregatedColumns() )
                                                     ->isTotals( true );

            $this->_addOrderStatusFilter( $totalsCollection, $filterData );
            $this->_addCustomFilter( $totalsCollection, $filterData );

            $totalsCollection->getSelect()
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

        $this->_prepareTotals( 'orders_count,total_qty,total_revenue_amount' );

        return parent::_prepareCollection();
    }
}
