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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Customer\Productscustomer;

class Grid extends \Lof\AdvancedReports\Block\Adminhtml\Grid\AbstractGrid {

    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = 'period';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';
    protected $_resource_grid_collection = null;

    public function _construct()
    {
        parent::_construct();
        $this->setId( 'topcustomersGrid' );
        $this->setCountTotals( true );
        $this->setFilterVisibility( false );
        $this->setPagerVisibility( true );
        $this->setUseAjax( false );
    }

    protected function _prepareColumns()
    {
        $this->addColumn( 'total_qty_ordered', [
            'header'          => __( 'Products Purchased' ),
            'index'           => 'total_qty_ordered',
            'type'            => 'number',
            'width'           => '100px',
            'totals_label'    => __( 'Total' ),
            'html_decorators' => [ 'nobr' ],
        ] );

        $this->addColumn( 'total_customer', [
            'header' => __( 'Number Of Customers' ),
            'index'  => 'total_customer',
            'width'  => '100px',
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
            'header'        => __( 'Total' ),
            'index'         => 'total_revenue_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'rate'          => $rate,
        ] );

        $this->addColumn( 'total_invoiced_amount', [
            'header'        => __( 'Invoiced' ),
            'index'         => 'total_invoiced_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'rate'          => $rate,
        ] );

        $this->addColumn( 'total_refunded_amount', [
            'header'        => __( 'Refunded' ),
            'index'         => 'total_refunded_amount',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'rate'          => $rate,
        ] );

        $this->addExportType( '*/*/exportProductscustomerCsv', __( 'CSV' ) );
        $this->addExportType( '*/*/exportProductscustomerExcel', __( 'Excel XML' ) );

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        $filterData  = $this->getFilterData();
        $report_type = $this->getReportType();

        $limit = $report_field = $filterData->getData( "limit", null );
        $limit = $filterData->getData( "limit", null );
        if ( ! $limit ) {
            $limit = $this->_defaultLimit;
        }
        $report_field = $filterData->getData( "report_field", null );
        $report_field = $report_field ? $report_field : "main_table.created_at";
        $this->setCulumnDate( $report_field );
        $this->setDefaultSort( "total_qty_ordered" );
        $this->setDefaultDir( "DESC" );

        $storeIds           = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create( 'Lof\AdvancedReports\Model\ResourceModel\Customer\Collection' )
                                                   ->prepareProductscustomerCollection()
                                                   ->setMainTableId( "total_qty_ordered" )
                                                   ->setDateColumnFilter( $this->_columnDate )
                                                   ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                   ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                   ->addStoreFilter( $storeIds )
                                                   ->setAggregatedColumns( $this->_getAggregatedColumns() );

        $this->_addOrderStatusFilter( $resourceCollection, $filterData );
        $this->_addCustomFilter( $resourceCollection, $filterData );

        if ( $customer_email = $filterData->getData( 'customer_email', null ) ) {
            $resourceCollection->filterByCustomerEmail( $customer_email );
        }

        $resourceCollection->getSelect()
                           ->order( new \Zend_Db_Expr( $this->getColumnOrder() . " " . $this->getColumnDir() ) );

        $resourceCollection->applyCustomFilter();

        $resourceCollection->setPageSize( (int) $this->getParam( $this->getVarNameLimit(), $limit ) );
        $resourceCollection->setCurPage( (int) $this->getParam( $this->getVarNamePage(), $this->_defaultPage ) );

        if ( $filterData->getData( 'show_empty_rows', false ) ) {
            $this->_reportsData->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData( 'year', null ),
                $filterData->getData( 'month', null ),
                $filterData->getData( 'day', null )
            );
        }
        $this->setCollection( $resourceCollection );
        if ( ! $this->_registry->registry( 'report_collection' ) ) {
            $this->_registry->register( 'report_collection', $resourceCollection );
        }
        $this->_prepareTotals( 'total_customer,total_revenue_amount,total_invoiced_amount,total_refunded_amount' ); //Add this Line with all the columns you want to have in totals bar

        return parent::_prepareCollection();
    }


    /**
     * Helper function to do after load modifications
     *
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk( 'afterLoad' );
        parent::_afterLoadCollection();
    }

    public function getGridUrl()
    {
        return $this->getUrl( '*/*/productscustomer', [ '_current' => true ] );
    }

}
