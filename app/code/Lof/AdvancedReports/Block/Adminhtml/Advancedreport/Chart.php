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

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport;

class Chart extends \Magento\Backend\Block\Template {
    protected $_hide_chart = false;
    protected $_helperData;
    protected $_registry = null;
    protected $_objectManager;
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * Chart constructor.
     *
     * @param \Magento\Backend\Block\Template\Context     $context
     * @param \Lof\AdvancedReports\Helper\Data            $helperData
     * @param \Magento\Framework\Registry                 $registry
     * @param \Magento\Framework\ObjectManagerInterface   $objectManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lof\AdvancedReports\Helper\Data $helperData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    )
    {
        $this->_helperData     = $helperData;
        $this->_storeManager   = $context->getStoreManager();
        $this->_objectManager  = $objectManager;
        $this->_registry       = $registry;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct( $context, $data );
    }

    /**
     * @param string $report_type
     * @param string $group
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChartBlock( $report_type = "", $group = "" )
    {
        if ( $report_type && $group ) {
            $chart_block = $this->getLayout()->createBlock( 'lofadvancedreports/adminhtml_advancedreport_' . $group . '_' . $report_type . '_chart',
                'lofadvancedreports_adminhtml_advancedreport_' . $group . '_' . $report_type . '.chart' );

            $period_type = $this->getPeriodType();
            $filter_data = $this->getFilterData();
            $sort_by     = $this->getCulumnOrder();
            $dir         = $this->getOrderDir();

            $chart_block->setReportType( $report_type );
            $chart_block->setPeriodType( $period_type );
            $chart_block->setFilterData( $filter_data );
            $chart_block->setCulumnOrder( $sort_by );
            $chart_block->setOrderDir( $dir );

            return $chart_block->initChart();
        }

        return false;
    }

    protected function _initChartData( $report_type = "", $group = "" )
    {
        if ( $chart_block = $this->getChartBlock( $report_type, $group ) ) {
            $chart_items_array = $chart_block->getChartItems();
            $initChartHtml     = $chart_block->getInitChart();
            $chartHtml         = $chart_block->getReportChart();
            $settings          = $chart_block->getSettings();

            $this->setChartItems( $chart_items_array );
            $this->setInitChart( $initChartHtml );
            $this->setReportChart( $chartHtml );
            $this->setSettings( $settings );
        }
    }

    public function getGoogleApi()
    {
        return $this->_objectManager->get( 'Lof\AdvancedReports\Helper\GoogleChart\Area' )->buildGoogleApiScript();
    }

    public function isHideChart()
    {
        return $this->_hide_chart;
    }

    public function setHideChart( $state = false )
    {
        $this->_hide_chart = $state;
    }

    public function getChartSettings( $settings = [], $data_items = [] )
    {
        $html = "";
        if ( $settings && $data_items ) {
            $function_name = isset( $settings['callback_func'] ) ? $settings['callback_func'] : 'drawChart';
            foreach ( $data_items as $data_item ) {
                if ( isset( $data_item['id'] ) && ( $chart_id = $data_item['id'] ) ) {
                    if ( isset( $data_item['isDefault'] ) && $data_item['isDefault'] ) {
                        $tmp_function_name = $function_name;
                    } else {
                        $tmp_function_name = $function_name . ucfirst( $chart_id );
                    }
                    $html .= '<li><a href="javascript:;" class="draw-chart" onClick="' . $tmp_function_name . '()">' . __( "Chart <strong>" . $data_item['label'] . "</strong>" ) . '</a>
                </li>';
                }
            }
        }

        return $html;
    }

    /**
     * @param int    $datetime
     * @param string $period_type
     * @return int|mixed
     * @throws \Exception
     */
    public function getDateFormat( $datetime = 0, $period_type = "" )
    {
        $filterData  = $this->getFilterData();
        $filter_from = $filterData->getData( 'filter_from', null );
        $filter_to   = $filterData->getData( 'filter_to', null );

        $period_type = $period_type ? $period_type : $this->getPeriodType();
        switch ( $period_type ) {
            case 'week' :
                // $date_range = $this->_helperData->getWeekRange( $datetime );
                // if ( count( $date_range ) == 2 ) {
                //     if ( strtotime( $date_range[0] ) < strtotime( $filter_from ) ) {
                //         $date_range[0] = date( "Y-m-d", strtotime( $filter_from ) );
                //     }
                //
                //     if ( strtotime( $date_range[1] ) > strtotime( $filter_to ) ) {
                //         $date_range[1] = date( "Y-m-d", strtotime( $filter_to ) );
                //     }
                //
                //     if ( $this->getColumn()->getGmtoffset() || $this->getColumn()->getTimezone() ) {
                //         $start_date = $this->_localeDate->date( new \DateTime( $date_range[0] ) );
                //         $end_date   = $this->_localeDate->date( new \DateTime( $date_range[1] ) );
                //     } else {
                //         $start_date = $this->_localeDate->date( new \DateTime( $date_range[0] ), null, false );
                //         $end_date   = $this->_localeDate->date( new \DateTime( $date_range[1] ), null, false );
                //     }
                //
                //     $start_date = $this->dateTimeFormatter->formatObject( $start_date, $format, $this->localeResolver->getLocale() );
                //     $end_date   = $this->dateTimeFormatter->formatObject( $end_date, $format, $this->localeResolver->getLocale() );
                //
                //
                //     $data = $start_date . " - " . $end_date;
                //
                //     $data_filter_from = date( "m/d/Y", strtotime( $start_date ) );
                //     $data_filter_to   = date( "m/d/Y", strtotime( $end_date ) );
                // }
                break;
            case 'weekday':
                $dates    = [
                    6 => __( "Sunday" ),
                    0 => __( "Monday" ),
                    1 => __( "Tuesday" ),
                    2 => __( "Wednesday" ),
                    3 => __( "Thursday" ),
                    4 => __( "Friday" ),
                    5 => __( "Saturday" ),
                ];
                $datetime = isset( $dates[ $datetime ] ) ? $dates[ $datetime ] : $datetime;
                break;
            case 'month' :
                $dateFormat = 'yyyy-MM';
                break;
            case 'year' :
                $dateFormat = 'yyyy';
                break;
            case 'hour' :
            default:
                break;
        }

        return $datetime;
    }

    public function getConfig( $key, $default = '' )
    {
        if ( $this->hasData( $key ) ) {
            return $this->getData( $key );
        }
        $result = $this->_helperData->getConfig( $key );
        if ( $result != null ) {
            return $result;
        }

        return $default;
    }
}
