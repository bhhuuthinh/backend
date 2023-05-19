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

namespace Lof\AdvancedReports\Helper\GoogleChart; 

class  Area extends \Lof\AdvancedReports\Helper\GoogleChart\AbstractChart
{


  public function  buildOptions($settings = array()) {

    $background = $this->_helperData->getConfig("charts_settings/background_color"); 
    $heading_title = isset($settings['heading_title'])?$settings['heading_title']:"";
    $haxis = isset($settings['haxis'])?$settings['haxis']:"";
    $vaxis = isset($settings['vaxis'])?$settings['vaxis']:"";
    $html = 'var options = {
                pieSliceText: "value",';
    if($background) {
      $html .= 'backgroundColor: "#'.$background.'",';
    }

    $html .= 'title: "'.$heading_title.'",';

    if($haxis) {
      $html .= 'hAxis: '.$haxis.',';
    }
    if($vaxis) {
      $html .= 'vAxis: '.$vaxis.',';
    }
    $html .='};';

    return $html;
  }
	public function buildChart($data = null, $settings = array()) { 
		$html = '';
    $function_name = isset($settings['callback_func'])?$settings['callback_func']:'drawChart';
    $data_columns = isset($settings['columns'])?$settings['columns']:array();
    $data_columns_array = isset($settings['columns_array'])?$settings['columns_array']:array();
    $filter_year = isset($settings['filter_year'])?$settings['filter_year']:false;
    $filter_month = isset($settings['filter_month'])?$settings['filter_month']:false;
    $filter_day = isset($settings['filter_day'])?$settings['filter_day']:false;
    $heading_title = isset($settings['heading_title'])?$settings['heading_title']:"";
    $chart_container = isset($settings['chart_container'])?$settings['chart_container']:"chart_container";
    $prefix = isset($settings['prefix'])?$settings['prefix']:"";
    $data_charts_array = isset($data['chart_items_array'])?$data['chart_items_array']:array();
    $data_charts = isset($data['chart_items'])?$data['chart_items']:array();

    //Build single chart function
    if($data_columns && $data_charts) {
        $html = $this->buildChartFunction($function_name, $data_columns, $data_charts, $settings, $prefix);
    }
    //Build array chart functions
    if($data_columns_array && $data_charts_array){
        foreach($data_charts_array as $i => $data_item) {
            if(isset($data_item['id']) && ($chart_id = $data_item['id'])){
               $tmp_data_columns = $data_columns_array[$chart_id];
               $tmp_data_charts = $data_item['data'];
               $tmp_prefix = isset($data_item['prefix'])?$data_item['prefix']:$prefix;
               if(isset($data_item['isDefault']) && $data_item['isDefault']) {
                  $tmp_function_name = $function_name;
               } else {
                  $tmp_function_name = $function_name.ucfirst($chart_id);
               }
               $tmp_settings = $settings;
               $tmp_settings['heading_title'] = (isset($data_item['label'])&&$data_item['label'])?$data_item['label']:$tmp_settings['heading_title'];

               $html .= $this->buildChartFunction($tmp_function_name, $tmp_data_columns, $tmp_data_charts, $tmp_settings, $tmp_prefix);
               $html .= "\n\n";

            }
        }
    }
		return $html;
	}

  public function buildChartFunction($function_name, $data_columns, $data_charts, $settings, $prefix = "" ) {
      $chart_container = isset($settings['chart_container'])?$settings['chart_container']:"chart_container";

      $html =  "/** @Function Name {$function_name} **/\n";
      $html .='function '.$function_name.'() {';

      if($data_columns && $data_charts) {
        $html .= 'var data = google.visualization.arrayToDataTable([';
        $html .= '[';
        $tmp = array();

        foreach($data_columns as $column_name) {
            if(strpos($column_name, "{") === false && strpos($column_name, "}") === false) {
              $tmp[] = "'".$column_name."'";
            } else {
              $tmp[] = $column_name;
            }
        }

        $html .= implode(",", $tmp);
        $html .= '],';

        $html .= implode(",", $data_charts);

        $html .= ']);';

        if($prefix) {
          $html .= "var formatter = new google.visualization.NumberFormat({prefix: '".$prefix."'});";
          $html .= 'formatter.format(data, 1);';
        }
        
        $html .= $this->buildOptions($settings);

        if(!$prefix) {
          $html .= 'if(typeof(options.vAxis.format) !== "undefined"){
                options.vAxis.format = "#";
              }';
        }

        $html .= 'var chart = new google.visualization.AreaChart(document.getElementById("'.$chart_container.'"));
          chart.draw(data, options);';
      }
      $html .= '}';
      return $html;
  }
}