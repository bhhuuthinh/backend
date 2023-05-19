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

class Chart extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Chart
{   
    public function _toHtml() {
        $result_collection = $this->_registry->registry('report_collection'); //Get result report collection
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return "";

        $this->initChart();

        return parent::_toHtml();
    }

    public function initChart() {
        $result_collection = $this->_registry->registry('report_collection');//Get result report collection
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return false;

        $this->initProductReportChart();

        return $this;
    }
    
    public function initProductReportChart() {
      $settings = array();
      $data = array();
      $font_color = $this->getConfig('charts_settings/font_color','#226767');   
      $font_size = $this->getConfig('charts_settings/font_size',14);
      $chart_color = $this->getConfig('charts_settings/color','#333');

      $settings['columns_array'] = array("sales_total" => array(__("Date"), __("Sales Earnings"), '{ role: "style" }'),
                                       "orders" => array(__("Date"), __("Sales Count"), '{ role: "style" }'),
                                       "items" => array(__("Date"), __("QTY Orders"), '{ role: "style" }')
                                      );

      $settings['heading_title'] = __("Sales By Product");
      $settings['chart_color'] =  $chart_color;
      $settings['prefix'] = $this->_localeCurrency->getCurrency($this->_storeManager->getStore()->getCurrentCurrencyCode())->getSymbol(); 
      $settings['haxis'] = '{title: "'.__("Date").'", titleTextStyle: {color: "#'.$font_color.'"}, textStyle: {color: "#'.$font_color.'", fontSize:'.$font_size.'}}';
      $settings['vaxis'] = '{minValue: 0, format: "'.$settings['prefix'].'#", textStyle: {color: "#'.$font_color.'", fontSize:'.$font_size.'}}';
      $settings['chart_class'] = '';
      $settings['chart_width'] = '300px';
      $settings['chart_height'] = '300px';

      $chart_items_1 = $this->_getProductReportChartData($settings, "total_revenue_amount");
      $chart_items_2 = $this->_getProductReportChartData($settings, "orders_count");
      $chart_items_3 = $this->_getProductReportChartData($settings, "total_qty");

      $data['chart_items_array'] = array(array("data" => $chart_items_1, 
                                        "id"    => "sales_total",
                                        'isDefault' => true,
                                        "prefix"=> $settings['prefix'],
                                        "label" => __("Revenue")
                                        ),
                                  array("data"  => $chart_items_2, 
                                        "id"    => "orders",
                                        "prefix"=> "",
                                        "label" => __("Orders Count")
                                        ),
                                  array("data"  => $chart_items_3,
                                        "id"    => "items",
                                        "prefix"=> "",
                                        "label" => __("QTY Ordered")
                                        )
                                );

      $initChartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Area')->initGoogleChart($settings);
      $chartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Area')->buildChart($data, $settings);

      $this->setInitChart($initChartHtml);
      $this->setChartItems($data['chart_items_array']);
      $this->setReportChart($chartHtml);
      $this->setSettings($settings);
    }

    protected function _getProductReportChartData( $settings = array(), $show_by = "total_revenue_amount") {
        $filterData = $this->getFilterData();
        $chart_color = isset($settings['chart_color'])?$settings['chart_color']: '#333';

        $reports = array();
        $tmp = array();

        $result_collection = $this->_registry->registry('report_collection'); //Get result report collection
        $reports = $this->_registry->registry('report_items'); //Get result report items array
        if($result_collection && !$reports) {
          $period_type = "period";
          if("month" == $this->getPeriodType()){
              $period_type = "period_sort";
          }
          $reports = $result_collection->getArrayItems($period_type);
          if($reports){
            $this->_registry->register('report_items', $reports);
          }
        }

        //Init chart item data
    
        if($reports) {
          ksort($reports);
          foreach($reports as $key=>$item) {
              if($item) {
                $period = $item->getData("period");
                $datefield = $this->getDateFormat($period);
                $tmp[] = "['".$datefield."', ".$item->getData($show_by).", 'color:#".$chart_color."']";
              }
            }
        }
        
        return $tmp;
    }
}