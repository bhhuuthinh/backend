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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Products\Productsoldtogether;

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
        // $this->setCountTotals(true);
        $this->setFilterVisibility( true );
        $this->setPagerVisibility( true );
        $this->setId( 'productSoldTogetherReportGrid' );
        $this->setUseAjax( false );
    }

    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Products\Collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn( 'original_name', [
            'header'          => __( 'Product Name' ),
            'width'           => '215px',
            'index'           => 'original_name',
            'type'            => 'text',
            'filter'          => false,
            'filter_data'     => $this->getFilterData(),
            'totals_label'    => __( 'Total' ),
            'html_decorators' => [ 'nobr' ],
        ] );
        $this->addColumn( 'original_sku', [
            'header' => __( 'Product SKU' ),
            'type'   => 'text',
            'filter' => false,
            'index'  => 'original_sku',
        ] );

        $this->addColumn( 'bought_with_name', [
            'header'      => __( 'Product Name' ),
            'width'       => '215px',
            'index'       => 'bought_with_name',
            'type'        => 'text',
            'filter'      => false,
            'filter_data' => $this->getFilterData(),
        ] );

        $this->addColumn( 'bought_with_sku', [
            'header' => __( 'Product SKU' ),
            'type'   => 'text',
            'filter' => false,
            'index'  => 'bought_with_sku',
        ] );

        $this->addColumn( 'times_bought_together', [
            'header' => __( 'Times Bought Together' ),
            'filter' => false,
            'index'  => 'times_bought_together',
            'type'   => 'number',
            'total'  => 'sum',
        ] );

        $this->addExportType( '*/*/exportProductSoldTogetherCsv', __( 'CSV' ) );
        $this->addExportType( '*/*/exportProductSoldTogetherExcel', __( 'Excel XML' ) );

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {

        $filterData  = $this->getFilterData();
        $report_type = $this->getReportType();

        $report_field = $filterData->getData( "report_field", null );
        $report_field = $report_field ? $report_field : "main_table.created_at";
        $this->setCulumnDate( $report_field );
        $this->setDefaultDir( "DESC" );

        $storeIds           = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create( $this->getResourceCollectionName() )
                                                   ->prepareProductSoldTogetherCollection()
                                                   ->setMainTableId( "main_table.product_id" )
                                                   ->setPeriodType( $this->getPeriodType() )
                                                   ->setDateColumnFilter( $this->_columnDate )
                                                   ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                   ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                   ->addStoreFilter( $storeIds )
                                                   ->setAggregatedColumns( $this->_getAggregatedColumns() );

        $this->_addOrderStatusFilter( $resourceCollection, $filterData );
        $this->_addCustomFilter( $resourceCollection, $filterData );

        $resourceCollection->applyOrderStatusFilter();
        $this->setCollection( $resourceCollection );

        return parent::_prepareCollection();
    }
}
