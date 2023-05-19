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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Sales\Dayofweek;

class Chart extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Chart
{ 
 
    public function _toHtml() {
        $result_collection = $this->_registry->registry('report_collection');  //Get result report collection
 
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return "";

        $this->initChart();

        return parent::_toHtml();
    }

    public function initChart() {
        $result_collection = $this->_registry->registry('report_collection');  //Get result report collection
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return false;

        $this->initWeekdayChart();

        return $this;
    }
    
    public function initWeekdayChart() {
      $settings = array();
      $data = array();

      $font_color = $this->getConfig('charts_settings/font_color','#226767');   
      $font_size = $this->getConfig('charts_settings/font_size',14);
      $chart_color = $this->getConfig('charts_settings/color','#333');


      $settings['columns_array'] = array(
                          "total_revenue_amount" => array(__("Week Day"), __("Revenue"), '{ role: "style" }'),
                          "total_grandtotal_amount" => array(__("Week Day"), __("Total"), '{ role: "style" }'),
                          "total_subtotal_amount" => array(__("Week Day"), __("Subtotal"), '{ role: "style" }'),
                            "orders_count" => array(__("Week Day"), __("Number Of Orders"), '{ role: "style" }'),
                            "total_qty_ordered" => array(__("Week Day"), __("Items Ordered"), '{ role: "style" }')
                          );

      $settings['heading_title'] = __("Sales Overview");
      $settings['chart_color'] = $chart_color;
      $settings['prefix'] = $this->_localeCurrency->getCurrency($this->_storeManager->getStore()->getCurrentCurrencyCode())->getSymbol(); 
      $settings['haxis'] = '{title: "'.__("Date").'", titleTextStyle: {color: "#'.$font_color.'"}, textStyle: {color: "#'.$font_color.'", fontSize:'.$font_size.'}}';
      $settings['vaxis'] = '{minValue: 0, format: "'.$settings['prefix'].'#", textStyle: {color: "#'.$font_color.'", fontSize:'.$font_size.'}}';
      $settings['chart_class'] = '';
      $settings['chart_width'] = '900px';
      $settings['chart_height'] = '500px';

      $chart_items_1 = $this->_getWeekdayChartData($settings, "total_revenue_amount");
      $chart_items_2 = $this->_getWeekdayChartData($settings, "total_grandtotal_amount");
      $chart_items_3 = $this->_getWeekdayChartData($settings, "total_subtotal_amount");
      $chart_items_4 = $this->_getWeekdayChartData($settings, "orders_count");
      $chart_items_5 = $this->_getWeekdayChartData($settings, "total_qty_ordered");

      $data['chart_items_array'] = array(
                                  array("data" => $chart_items_1, 
                                        "id"    => "total_revenue_amount",
                                        'isDefault' => true,
                                        "prefix"=> $settings['prefix'],
                                        "label" => __("Revenue")
                                        ),
                                  array("data" => $chart_items_2, 
                                        "id"    => "total_grandtotal_amount",
                                        "prefix"=> $settings['prefix'],
                                        "label" => __("Total")
                                        ),
                                  array("data" => $chart_items_3, 
                                        "id"    => "total_subtotal_amount",
                                        "prefix"=> $settings['prefix'],
                                        "label" => __("Subtotal")
                                        ),
                                  array("data"  => $chart_items_4, 
                                        "id"    => "orders_count",
                                        "prefix"=> "",
                                        "label" => __("Number Of Orders")
                                        ),
                                  array("data"  => $chart_items_5,
                                        "id"    => "total_qty_ordered",
                                        "prefix"=> "",
                                        "label" => __("Items Ordered")
                                        )
                                );

      $initChartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Column')->initGoogleChart($settings);
      $chartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Column')->buildChart($data, $settings);

      $this->setInitChart($initChartHtml);
      $this->setChartItems($data['chart_items_array']);
      $this->setReportChart($chartHtml);
      $this->setSettings($settings);
    }

    protected function _getWeekdayChartData( $settings = array(), $show_by = "total_revenue_amount") {
        $filterData = $this->getFilterData(); 
        $chart_color = isset($settings['chart_color'])?$settings['chart_color']: '#333';

        $reports = array();
        $tmp = array();

        $result_collection = $this->_registry->registry('report_collection'); //Get result report collection
        $reports = $this->_registry->registry('report_items');//Get result report items array 
        if($result_collection && !isset($reports)) {
          $period_type = "period";
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

                $datefield = $this->getDateFormat($period, "weekday");
                $tmp[] = "['".$datefield."', ".$item->getData($show_by).", 'color:#".$chart_color."']";
              }
            }
        } 
 
        return $tmp;
    }

}