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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Order\Abandoned;

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
        $this->setPagerVisibility( true );
        $this->setId( 'abandonedGrid' );
        $this->setUseAjax( false );
        $this->setDefaultSort( "created_at" );
        $this->setDefaultDir( "DESC" );
        $this->setSaveParametersInSession( true );
        $this->setVarNameFilter( 'order_filter' );
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Order\Abandoned\Collection';
    }

    protected function _prepareColumns()
    {
        $is_export  = isset( $this->_isExport ) ? $this->_isExport : 0;
        $filterData = $this->getFilterData();
        $this->addColumn( 'period', [
            'header'          => __( 'Period' ),
            'index'           => 'period',
            'width'           => 100,
            'filter'          => false,
            'show_link'       => true,
            'is_export'       => $is_export,
            'data_filter'     => [ 'date_range' => [ 'filter_from', 'filter_to' ], 'route' => '*/advancedreports_order/abandoneddetailed/' ],
            'filter_data'     => $this->getFilterData(),
            'period_type'     => $this->getPeriodType(),
            'renderer'        => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\Dateperiod',
            'totals_label'    => __( 'Total' ),
            'html_decorators' => [ 'nobr' ],
        ] );


        if ( $this->getRequest()->getParam( 'website' ) ) {
            $storeIds = $this->_storeManager->getWebsite( $this->getRequest()->getParam( 'website' ) )->getStoreIds();
        } elseif ( $this->getRequest()->getParam( 'group' ) ) {
            $storeIds = $this->_storeManager->getGroup( $this->getRequest()->getParam( 'group' ) )->getStoreIds();
        } elseif ( $this->getRequest()->getParam( 'store' ) ) {
            $storeIds = [ (int) $this->getRequest()->getParam( 'store' ) ];
        } else {
            $storeIds = [];
        }
        $this->setStoreIds( $storeIds );
        $filterData = $this->getFilterData();

        $currencyCodeParam = $filterData->getData( 'currency_code' ) ?: null;
        $currencyCode      = $this->getCurrentCurrencyCode( $currencyCodeParam );
        $rate              = $this->getRate( $currencyCode ) ?: 1;

        $this->addColumn( 'total_cart', [
            'header' => __( 'Total Carts' ),
            'width'  => '80px',
            'type'   => 'number',
            'index'  => 'total_cart',
            'filter' => false,
            'total'  => 'sum',
        ] );

        $this->addColumn( 'total_completed_cart', [
            'header' => __( 'Completed Carts' ),
            'width'  => '80px',
            'align'  => 'right',
            'index'  => 'total_completed_cart',
            'filter' => false,
            'type'   => 'number',
            'total'  => 'sum',
        ] );

        $this->addColumn( 'total_abandoned_cart', [
            'header' => __( 'Abandoned Carts' ),
            'width'  => '80px',
            'align'  => 'right',
            'index'  => 'total_abandoned_cart',
            'filter' => false,
            'type'   => 'number',
            'total'  => 'sum',
        ] );


        $this->addColumn( 'abandoned_cart_total_amount', [
            'header'        => __( 'Abandoned Carts Total' ),
            'width'         => '80px',
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'abandoned_cart_total_amount',
            'filter'        => false,
            'renderer'      => 'Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency',
            'rate'          => $rate,
            'total'         => 'sum',
        ] );

        $this->addColumn( 'abandoned_rate', [
            'header'   => __( 'Abandonment Rate' ),
            'width'    => '80px',
            'index'    => 'abandoned_rate',
            'renderer' => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\AbandonedRate',
            'filter'   => false,
        ] );


        $this->addExportType( '*/*/exportAbandonedCsv', __( 'CSV' ) );
        $this->addExportType( '*/*/exportAbandonedExcel', __( 'Excel XML' ) );

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
        $this->setDefaultDir( "ASC" );


        $order = $this->getColumnOrder();
        if ( "month" == $this->getPeriodType() ) {
            $order = "main_table.created_at";
        }


        $storeIds           = $this->_getStoreIds();
        $resourceCollection = $this->_objectManager->create( 'Lof\AdvancedReports\Model\ResourceModel\Order\Abandoned\Collection' )
                                                   ->prepareCartCollection()
                                                   ->setDateColumnFilter( $this->_columnDate )
                                                   ->setPeriodType( $this->getPeriodType() )
                                                   ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                   ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                   ->addStoreFilter( $storeIds )
                                                   ->setAggregatedColumns( $this->_getAggregatedColumns() );


        $this->_addCustomFilter( $resourceCollection, $filterData );

        $resourceCollection->getSelect()
                           ->order( new \Zend_Db_Expr( $order . " " . $this->getColumnDir() ) );

        $resourceCollection->applyCustomFilter();

        $resourceCollection->setPageSize( (int) $this->getParam( $this->getVarNameLimit(), $limit ) );
        $resourceCollection->setCurPage( (int) $this->getParam( $this->getVarNamePage(), $this->_defaultPage ) );


        //Completed Carts Collection
        $resourceComletedCartCollection = $this->_objectManager->create( 'Lof\AdvancedReports\Model\ResourceModel\Order\Abandoned\Collection' )
                                                               ->prepareCompletedCartCollection()
                                                               ->setDateColumnFilter( $this->_columnDate )
                                                               ->setPeriodType( $this->getPeriodType() )
                                                               ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                               ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                               ->addStoreFilter( $storeIds )
                                                               ->setAggregatedColumns( $this->_getAggregatedColumns() );

        $resourceComletedCartCollection->applyCustomFilter();
        $completed_cart_select = $resourceComletedCartCollection->getSelect();


        //End Completed Carts Collection
        //Abandoned Carts Collection
        $resourceAbandonedCartCollection = $this->_objectManager->create( 'Lof\AdvancedReports\Model\ResourceModel\Order\Abandoned\Collection' )
                                                                ->prepareAbandonedCartCollection()
                                                                ->setDateColumnFilter( $this->_columnDate )
                                                                ->setPeriodType( $this->getPeriodType() )
                                                                ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                                ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                                ->addStoreFilter( $storeIds )
                                                                ->setAggregatedColumns( $this->_getAggregatedColumns() );
        $resourceAbandonedCartCollection->applyCustomFilter();
        $abandoned_cart_select = $resourceAbandonedCartCollection->getSelect();
        //echo $abandoned_cart_select;die();


        //End Abandoned Carts Collection
        $resourceCollection->joinCartCollection( $completed_cart_select, 'cc', 'period', [ "total_completed_cart", "completed_cart_total_amount" ] );
        $resourceCollection->joinCartCollection( $abandoned_cart_select, 'abc', 'period', [ "total_abandoned_cart", "abandoned_cart_total_amount" ] );

        $resourceCollection->setMainTableId( $resourceCollection->getPeriodDateField() );
        $this->setCollection( $resourceCollection );

        //echo $resourceCollection->getSelect();die();
        if ( ! $this->_registry->registry( 'report_collection' ) ) {
            $this->_registry->register( 'report_collection', $resourceCollection );
        }

        $this->_prepareTotals( 'total_cart,total_completed_cart,total_abandoned_cart,abandoned_cart_total_amount' ); //Add this Line with all the columns you want to have in totals bar

        return parent::_prepareCollection();
    }

    public function getGridUrl()
    {
        return $this->getUrl( '*/*/abandoned', [ '_current' => true ] );
    }

}
