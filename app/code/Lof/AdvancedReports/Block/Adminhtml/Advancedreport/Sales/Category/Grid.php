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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Sales\Category;

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
        $this->addColumn( 'category_id', [
            'header'          => __( 'Category' ),
            'index'           => 'category_id',
            'width'           => 100,
            'renderer'        => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\Category',
            'totals_label'    => __( 'Total' ),
            'html_decorators' => [ 'nobr' ],
        ] );

        $this->addColumn( 'orders_count', [
            'header' => __( 'Sales Count' ),
            'index'  => 'orders_count',
            'type'   => 'number',
            'total'  => 'sum',
        ] );

        $this->addColumn( 'total_qty_ordered', [
            'header' => __( 'Qty Ordered' ),
            'index'  => 'total_qty_ordered',
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

        $this->addColumn( 'total_refunded_amount', [
            'header'        => __( 'Refunded' ),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_refunded_amount',
            'total'         => 'sum',
            'rate'          => $rate,
        ] );
        $this->addExportType( '*/*/exportSalesByCategoryCsv', __( 'CSV' ) );
        $this->addExportType( '*/*/exportSalesByCategoryExcel', __( 'Excel XML' ) );

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        $filterData  = $this->getFilterData();
        $report_type = $this->getReportType();
        $limit       = $filterData->getData( "limit", null );

        $report_field = $filterData->getData( "report_field", null );
        $report_field = $report_field ? $report_field : "main_table.created_at";
        $this->setCulumnDate( $report_field );
        $this->setDefaultSort( "category_id" );
        $this->setDefaultDir( "ASC" );
        $order = $this->getColumnOrder();
        if ( "month" == $this->getPeriodType() ) {
            $order = "main_table.created_at";
        }
        $currencyCode       = $this->getCurrentCurrencyCode( null );
        $storeIds           = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create( $this->getResourceCollectionName() )
                                                   ->setOrderRate( $currencyCode )
                                                   ->prepareCategoryReportCollection()
                                                   ->setDateColumnFilter( $this->_columnDate )
                                                   ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                   ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                   ->addCategoryIdFilter( $filterData->getData( 'category_id', null ) )
                                                   ->addStoreFilter( $storeIds );
        $resourceCollection->applyCategoryFilter();

        $this->_addOrderStatusFilter( $resourceCollection, $filterData );
        $this->_addCustomFilter( $resourceCollection, $filterData );

        $resourceCollection->getSelect()
                           ->order( new \Zend_Db_Expr( $order . " " . $this->getColumnDir() ) );

        $resourceCollection->applyCustomFilter();
        $resourceCollection->setPageSize( (int) $this->getParam( $this->getVarNameLimit(), $limit ) );
        $resourceCollection->setCurPage( (int) $this->getParam( $this->getVarNamePage(), $this->_defaultPage ) );

        if ( $this->getCountSubTotals() ) {
            $this->getSubTotals();
        }
        $this->setCollection( $resourceCollection );

        if ( ! $this->_registry->registry( 'report_collection' ) ) {
            $this->_registry->register( 'report_collection', $resourceCollection );
        }

        $this->_prepareTotals( 'orders_count,total_qty_ordered,total_revenue_amount,total_refunded_amount' );

        return parent::_prepareCollection();
    }
}
