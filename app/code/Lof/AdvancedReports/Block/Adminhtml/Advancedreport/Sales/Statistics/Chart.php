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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Sales\Statistics;

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

        $this->initStatisticsChart();

        return $this;
    }
    
    public function initStatisticsChart() {
      $settings = array();
      $data = array();
      $settings['columns_array'] = array();
      $font_color = $this->getConfig('charts_settings/font_color','#226767');   
      $font_size = $this->getConfig('charts_settings/font_size',14);
      $chart_color = $this->getConfig('charts_settings/color','#333');
 
      $settings['multi_columns_array'] = array(
                                      "date" => array("string", __("Date")),
                                      "avg_order_amount" => array("number", __("Order Amount (Avg)")),
                                      "avg_item_cost" => array("number", __("Item Price (Avg)"))
                                      );

      $settings['heading_title'] = __("Sales Statistics");
      $settings['vheading_title'] = __("Avg. Price (%s)", $this->_storeManager->getStore()->getCurrentCurrencyCode());
      $settings['subtitle'] = __("Currency (%s)", $this->_storeManager->getStore()->getCurrentCurrencyCode());
      $settings['chart_color'] = $chart_color;
      $settings['prefix'] = $this->_localeCurrency->getCurrency($this->_storeManager->getStore()->getCurrentCurrencyCode())->getSymbol();  
      $settings['haxis'] = '{title: "'.__("Date").'", titleTextStyle: {color: "#'.$font_color.'"}, textStyle: {color: "#'.$font_color.'", fontSize:'.$font_size.'}}';
      $settings['vaxis'] = '{minValue: 0, format: "'.$settings['prefix'].'#", textStyle: {color: "#'.$font_color.'", fontSize:'.$font_size.'}}';
      $settings['chart_class'] = '';
      $settings['chart_width'] = '900px';
      $settings['chart_height'] = '500px';
      $settings['format_index'] = array(1, 2);

      $fields = array("avg_order_amount", "avg_item_cost");
      $data['chart_items'] = $this->_getStatisticsChartData($settings, $fields);

      $initChartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Line')->initGoogleChart($settings);
      $chartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Line')->buildChart($data, $settings);

      $this->setInitChart($initChartHtml);
      $this->setChartItems(false);
      $this->setReportChart($chartHtml);
      $this->setSettings($settings);
    }

    protected function _getStatisticsChartData( $settings = array(), $fields = array()) {
        $filterData = $this->getFilterData();
        $chart_color = isset($settings['chart_color'])?$settings['chart_color']: '#333';

        $reports = array();
        $tmp = array();

        if($fields) {
            $result_collection = $this->_registry->registry('report_collection');
            $reports = $this->_registry->registry('report_items');
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
              // ksort($reports);
              foreach($reports as $key=>$item) {
                  if($item) {
                    $period = $item->getData("period");
                    $datefield = $this->getDateFormat($period);
                    $tmp_fields = array();
                    foreach($fields as $field_key) {
                        $tmp_fields[] = $item->getData($field_key);
                    }
                    $tmp[] = "['".$datefield."', ".implode(", ", $tmp_fields)."]";
                  }
                }
            }
        }
        return $tmp;
    }

}