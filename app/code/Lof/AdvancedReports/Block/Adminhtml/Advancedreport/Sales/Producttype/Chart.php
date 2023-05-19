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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Sales\Producttype;

class Chart extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Chart {
    public function _toHtml()
    {
        $result_collection = $this->_registry->registry( 'report_collection' );
        if ( ! $result_collection || ( 0 >= $result_collection->getSize() ) ) {
            return "";
        }

        $this->initChart();

        return parent::_toHtml();
    }

    public function initChart()
    {
        $result_collection = $this->_registry->registry( 'report_collection' );
        if ( ! $result_collection || ( 0 >= $result_collection->getSize() ) ) {
            return false;
        }

        $this->initProducttypeChart();

        return $this;
    }

    public function initProducttypeChart()
    {
        $report_type = $this->getReportType();
        $filterData  = $this->getFilterData();

        $settings = [];
        $data     = [];

        $settings['columns_array'] = [
            "total_qty_ordered"   => [ __( "Product Type" ), __( "Qty Ordered" ) ],
            "orders_count"        => [ __( "Product Type" ), __( "Sales Count" ) ],
            "total_income_amount" => [ __( "Product Type" ), __( "Total" ) ],
        ];

        $settings['heading_title'] = __( "Sales By Product Type" );
        $settings['chart_class']   = '';
        $settings['chart_width']   = '300px';
        $settings['chart_height']  = '300px';
        $settings['prefix']        = $this->_localeCurrency->getCurrency( $this->_storeManager->getStore()->getCurrentCurrencyCode() )->getSymbol();

        $chart_items_1 = $this->_getProducttypeChartData( $settings, "total_qty_ordered" );
        $chart_items_2 = $this->_getProducttypeChartData( $settings, "orders_count" );
        $chart_items_3 = $this->_getProducttypeChartData( $settings, "total_income_amount" );

        $data['chart_items_array'] = [
            [
                "data"      => $chart_items_1,
                "id"        => "total_qty_ordered",
                'isDefault' => true,
                "prefix"    => "",
                "label"     => __( "Qty Ordered" ),
            ],
            [
                "data"   => $chart_items_2,
                "id"     => "total_qty_ordered",
                "prefix" => "",
                "label"  => __( "Sales Count" ),
            ],
            [
                "data"   => $chart_items_3,
                "id"     => "total_income_amount",
                "prefix" => $settings['prefix'],
                "label"  => __( "Total" ),
            ],
        ];

        $initChartHtml = $this->_objectManager->get( 'Lof\AdvancedReports\Helper\GoogleChart\Pie' )->initGoogleChart( $settings );
        $chartHtml     = $this->_objectManager->get( 'Lof\AdvancedReports\Helper\GoogleChart\Pie' )->buildChart( $data, $settings );

        $this->setChartItems( $data['chart_items_array'] );
        $this->setInitChart( $initChartHtml );
        $this->setReportChart( $chartHtml );
        $this->setSettings( $settings );
    }

    protected function _getProducttypeChartData( $settings = [], $show_by = "total_qty_ordered" )
    {
        $filterData = $this->getFilterData();
        $reports    = [];
        $tmp        = [];

        $result_collection = $this->_registry->registry( 'report_collection' );
        $reports           = $this->_registry->registry( 'report_items' ); //Get result report items array
        if ( $result_collection && ! $reports ) {
            $reports = $result_collection->getArrayItems( "product_type" );
            if ( $reports ) {
                $this->_registry->register( 'report_items', $reports );
            }
        }

        //Init chart item data
        if ( $reports ) {
            foreach ( $reports as $key => $item ) {
                if ( $item ) {
                    $tmp[] = "['" . $key . "', " . (float) $item->getData( $show_by ) . "]";
                }
            }

        }

        return $tmp;
    }
}
