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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Order\Abandoneddetailed;

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
        $this->setFilterVisibility( true );
        $this->setId( 'abandoneddetailedGrid' );
        $this->setUseAjax( false );
        $this->setDefaultSort( "created_at" );
        $this->setDefaultDir( "DESC" );
        $this->setSaveParametersInSession( true );
        $this->setVarNameFilter( 'order_filter' );
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
        $this->setDefaultSort( "main_table.created_at" );
        $this->setDefaultDir( "ASC" );


        $order = $this->getColumnOrder();
        if ( "month" == $this->getPeriodType() ) {
            $order = "main_table.created_at";
        }

        $filter = $this->getParam( $this->getVarNameFilter(), [] );
        if ( $filter ) {
            $filter = base64_decode( $filter );
            parse_str( urldecode( $filter ), $data );
        }
        $storeIds = $this->_getStoreIds();
        if ( empty( $data ) ) {
            $data = null;
        }
        $resourceCollection = $this->_objectManager->create( 'Lof\AdvancedReports\Model\ResourceModel\Order\Abandoned\Collection' )
                                                   ->prepareAbandonedCartDetailCollection( $storeIds, $data )
                                                   ->setDateColumnFilter( $this->_columnDate )
                                                   ->addDateFromFilter( $filterData->getData( 'filter_from', null ) )
                                                   ->addDateToFilter( $filterData->getData( 'filter_to', null ) )
                                                   ->addStoreFilter( $storeIds )
                                                   ->setAggregatedColumns( $this->_getAggregatedColumns() );

        $resourceCollection->getSelect()
                           ->order( new \Zend_Db_Expr( $order . " " . $this->getColumnDir() ) );

        $resourceCollection->applyCustomFilter();

        $resourceCollection->setPageSize( (int) $this->getParam( $this->getVarNameLimit(), $limit ) );
        $resourceCollection->setCurPage( (int) $this->getParam( $this->getVarNamePage(), $this->_defaultPage ) );

        $resourceCollection->setMainTableId( "main_table.entity_id" );
        $this->setCollection( $resourceCollection );
        if ( ! $this->_registry->registry( 'report_collection' ) ) {
            $this->_registry->register( 'report_collection', $resourceCollection );
        }
        $this->_prepareTotals( 'items_qty,subtotal' );
        parent::_prepareCollection();
        $this->getCollection()->resolveCustomerNames();

        return $this;
    }

    /**
     * @param array $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection( $column )
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $skip  = [ 'subtotal', 'customer_name', 'email' ];

        if ( in_array( $field, $skip ) ) {
            return $this;
        }
        parent::_addColumnFilterToCollection( $column );

        return $this;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'customer_name',
            [
                'header'           => __( 'Customer' ),
                'index'            => 'customer_name',
                'sortable'         => false,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
            ]
        );

        $this->addColumn(
            'email',
            [
                'header'           => __( 'Email' ),
                'index'            => 'email',
                'sortable'         => false,
                'header_css_class' => 'col-email',
                'column_css_class' => 'col-email',
            ]
        );

        $this->addColumn(
            'items_count',
            [
                'header'           => __( 'Products' ),
                'index'            => 'items_count',
                'sortable'         => false,
                'type'             => 'number',
                'header_css_class' => 'col-number',
                'column_css_class' => 'col-number',
            ]
        );

        $this->addColumn(
            'items_qty',
            [
                'header'           => __( 'Quantity of Items' ),
                'index'            => 'items_qty',
                'sortable'         => false,
                'type'             => 'number',
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty',
            ]
        );

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

        $this->addColumn(
            'subtotal',
            [
                'header'           => __( 'Subtotal' ),
                'type'             => 'currency',
                'currency_code'    => $currencyCode,
                'index'            => 'subtotal',
                'sortable'         => false,
                'renderer'         => 'Magento\Reports\Block\Adminhtml\Grid\Column\Renderer\Currency',
                'rate'             => $rate,
                'header_css_class' => 'col-subtotal',
                'column_css_class' => 'col-subtotal',
            ]
        );

        $this->addColumn(
            'coupon_code',
            [
                'header'           => __( 'Applied Coupon' ),
                'index'            => 'coupon_code',
                'sortable'         => false,
                'header_css_class' => 'col-coupon',
                'column_css_class' => 'col-coupon',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header'           => __( 'Created' ),
                'type'             => 'datetime',
                'index'            => 'created_at',
                'filter_index'     => 'main_table.created_at',
                'sortable'         => false,
                'header_css_class' => 'col-created',
                'column_css_class' => 'col-created',
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header'           => __( 'Updated' ),
                'type'             => 'datetime',
                'index'            => 'updated_at',
                'filter_index'     => 'main_table.updated_at',
                'sortable'         => false,
                'header_css_class' => 'col-updated',
                'column_css_class' => 'col-updated',
            ]
        );

        $this->addColumn(
            'remote_ip',
            [
                'header'           => __( 'IP Address' ),
                'index'            => 'remote_ip',
                'sortable'         => false,
                'header_css_class' => 'col-ip',
                'column_css_class' => 'col-ip',
            ]
        );

        $this->addExportType( '*/*/exportAbandoneddetailedCsv', __( 'CSV' ) );
        $this->addExportType( '*/*/exportAbandoneddetailedExcel', __( 'Excel XML' ) );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl( '*/*/abandoneddetailed', [ '_current' => true ] );
    }

}
