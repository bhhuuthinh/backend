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
class  Line extends \Lof\AdvancedReports\Helper\GoogleChart\AbstractChart{

  public function  buildOptions($settings = array()) {
 
    $background = $this->_helperData->getConfig("charts_settings/background_color"); 
    $font_color = $this->_helperData->getConfig("charts_settings/font_color"); 
    $font_size = $this->_helperData->getConfig("charts_settings/font_size");  
    $font_size = isset($font_size)?$font_size:14; 
    $chart_color = $this->_helperData->getConfig("charts_settings/color"); 
 
    $heading_title = isset($settings['heading_title'])?$settings['heading_title']:"";
    $vheading_title = isset($settings['vheading_title'])?$settings['vheading_title']:"";
    $subtitle = isset($settings['subtitle'])?$settings['subtitle']:"";
    $chart_width = isset($settings['chart_width'])?$settings['chart_width']:"900";
    $chart_width = str_replace("px", "", $chart_width);
    $chart_height = isset($settings['chart_height'])?$settings['chart_height']:"500";
    $chart_height = str_replace("px", "", $chart_height);
    $html = "var options = {
                chart: {
                  title: '".$heading_title."',
                  subtitle: '".$subtitle."'
                },
                hAxis: {textStyle: {color: '#".$font_color."', fontSize:".$font_size."}},
                vAxis: {
                  // Adds titles to each axis.
                  0: {title: '".$vheading_title."'},
                  textStyle: {color: '#".$font_color."', fontSize:".$font_size."}
                },";

    if($background) {
      $html .= 'backgroundColor: "#'.$background.'",';
    }             
    $html .= "
                height: ".(int)$chart_height."
              };";

    return $html;
  }

	public function buildChart($data = null, $settings = array()) {
    $html = '';
    $function_name = isset($settings['callback_func'])?$settings['callback_func']:'drawChart';
    $data_columns = isset($settings['columns'])?$settings['columns']:array();
    $data_columns_array = isset($settings['columns_array'])?$settings['columns_array']:array();
    $data_multi_columns = isset($settings['multi_columns_array'])?$settings['multi_columns_array']:array();
    $filter_year = isset($settings['filter_year'])?$settings['filter_year']:false;
    $filter_month = isset($settings['filter_month'])?$settings['filter_month']:false;
    $filter_day = isset($settings['filter_day'])?$settings['filter_day']:false;
    $heading_title = isset($settings['heading_title'])?$settings['heading_title']:"";
    $chart_container = isset($settings['chart_container'])?$settings['chart_container']:"chart_container";
    $prefix = isset($settings['prefix'])?$settings['prefix']:"";
    $suffix = isset($settings['suffix'])?$settings['suffix']:"";
    $data_charts_array = isset($data['chart_items_array'])?$data['chart_items_array']:array();
    $data_charts = isset($data['chart_items'])?$data['chart_items']:array();
    //Build single chart function
    if($data_columns && $data_charts) {
        $html = $this->buildChartFunction($function_name, $data_columns, $data_charts, $settings, $prefix, false, $suffix);
    }
    //Build array chart functions
    if($data_columns_array && $data_charts_array){
        foreach($data_charts_array as $i => $data_item) {
            if(isset($data_item['id']) && ($chart_id = $data_item['id'])){
               $tmp_data_columns = $data_columns_array[$chart_id];
               $tmp_data_charts = $data_item['data'];
               $tmp_prefix = isset($data_item['prefix'])?$data_item['prefix']:$prefix;
               $tmp_suffix = isset($data_item['suffix'])?$data_item['suffix']:$suffix;
               if(isset($data_item['isDefault']) && $data_item['isDefault']) {
                  $tmp_function_name = $function_name;
               } else {
                  $tmp_function_name = $function_name.ucfirst($chart_id);
               }
               $tmp_settings = $settings;
               $tmp_settings['heading_title'] = (isset($data_item['label'])&&$data_item['label'])?$data_item['label']:$tmp_settings['heading_title'];

               $html .= $this->buildChartFunction($tmp_function_name, $tmp_data_columns, $tmp_data_charts, $tmp_settings, $tmp_prefix, false, $tmp_suffix);
               $html .= "\n\n";

            }
        }
    }
    //Build array chart functions
    if($data_multi_columns && $data_charts){
        $html = $this->buildChartFunction($function_name, $data_multi_columns, $data_charts, $settings, $prefix, true, $suffix);
    }
    return $html;
  }

  public function buildChartFunction($function_name, $data_columns, $data_charts, $settings, $prefix = "",  $is_multi = false, $suffix = "" ) {
      $chart_container = isset($settings['chart_container'])?$settings['chart_container']:"chart_container";
      $format_index = isset($settings['format_index'])?$settings['format_index']:array(1);
      $html =  "/** @Function Name {$function_name} **/\n";
      $html .='function '.$function_name.'() {';

      if($data_columns && $data_charts) {
        if($is_multi) {
            $html .= 'var data = new google.visualization.DataTable();';
            foreach($data_columns as $column_item) {
                $html .= "\ndata.addColumn('".$column_item[0]."', '".$column_item[1]."');";
            }
            $html .= "\ndata.addRows([\n".implode(",", $data_charts)."\n]);";

        } else {
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
        }
        

        if($prefix) {
          $html .= "var formatter = new google.visualization.NumberFormat({prefix: '".$prefix."'});";
          if($format_index) {
            foreach($format_index as $fi) {
                $html .= 'formatter.format(data, '.$fi.');';
            }
          } else {
            $html .= 'formatter.format(data, 1);';
          }
        }

        if($suffix) {
          $html .= "var formatter = new google.visualization.NumberFormat({fractionDigits: 2, suffix: '".$suffix."'});";
          if($format_index) {
            foreach($format_index as $fi) {
                $html .= 'formatter.format(data, '.$fi.');';
            }
          } else {
            $html .= 'formatter.format(data, 1);';
          }
        }
        
        $html .= $this->buildOptions($settings);

        if(!$prefix) {
          $html .= 'if(typeof(options.vAxis.format) !== "undefined"){
                options.vAxis.format = "#";
              }';
        }

        if(!$suffix) {
          $html .= 'if(typeof(options.vAxis.format) !== "undefined"){
                options.vAxis.format = "#";
              }';
        }

        $html .= 'var chart = new google.visualization.LineChart(document.getElementById("'.$chart_container.'"));
          chart.draw(data, options);';
      }
      $html .= '}';
      return $html;
  }
}