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

class Chart extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Chart
{   
    public function _toHtml() {
        $result_collection = $this->_registry->registry('report_collection');
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return "";

        $this->initChart();

        return parent::_toHtml();
    }

    public function initChart() {
        $result_collection = $this->_registry->registry('report_collection');
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return false;

        $this->initAbandonedChart();

        return $this;
    }
    
    public function initAbandonedChart() {
      $settings = array();
      $data = array();
      $settings['columns_array'] = array();
      //"date" => array(data_type, data_label). data_type: string, number, date, datetime or timeofday
      $settings['multi_columns_array'] = array(
                                      "date" => array("string", __("Date")),
                                      "abandoned_rate" => array("number", __("Abandonment Rate"))
                                      );

      $settings['heading_title'] = __("Abandoned Carts");
      $settings['vheading_title'] = __("Abandonment Rate (%)");
      $settings['subtitle'] = "";
      $settings['chart_color'] = '#333';
      $settings['suffix'] = "%";
      $settings['haxis'] = '{title: "'.__("Date").'", titleTextStyle: {color: "#333"}}';
      $settings['vaxis'] = "{minValue: 0, format: '#\'%\''}";
      $settings['chart_class'] = '';
      $settings['chart_width'] = '900px';
      $settings['chart_height'] = '500px';
      $settings['format_index'] = array(1);

      $data['chart_items'] = $this->_getAbandonedChartData($settings); 

      $initChartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Line')->initGoogleChart($settings);
      $chartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Line')->buildChart($data, $settings);

      $this->setInitChart($initChartHtml);
      $this->setChartItems(false);
      $this->setReportChart($chartHtml);
      $this->setSettings($settings);
    }

    protected function _getAbandonedChartData( $settings = array()) {
        $filterData = $this->getFilterData();
        $chart_color = isset($settings['chart_color'])?$settings['chart_color']: '#333';

        $reports = array();
        $tmp = array();

        $result_collection = $this->_registry->registry('report_collection');
        $reports = $this->_registry->registry('report_collection');
        if($result_collection && !$reports) {
          $period_type = "period";
          if("month" == $this->getPeriodType()){
              $period_type = "period_sort";
          }
          $reports = $result_collection->getArrayItems($period_type); 
          $this->_registry->register('report_collection', $reports); 
        }

        //Init chart item data
      
        if($reports) {
          // ksort($reports);
          foreach($reports as $key=>$item) {
              if($item) {
                $period = $item->getData("period");
                $datefield = $this->getDateFormat($period);
                $total_cart = $item->getData("total_cart");
                $total_abandoned_cart = $item->getData("total_abandoned_cart");
                $percent = 0;
                if($total_cart && (int)$total_cart > 0) {
                  $percent = round(((int)$total_abandoned_cart/(int)$total_cart)*100, 2);
                }
                $tmp[] = "['".$datefield."', ".$percent."]";
              }
            }
        }
        return $tmp;
    }

}