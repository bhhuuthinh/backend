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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Earning;

class Chart extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Chart
{  
    public function _toHtml() {
      	$settings = array();
    		$data = array(); 
        $result_collection = $this->_registry->registry('report_collection'); //Get result report collection  
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return false; 
        $font_color = $this->getConfig('charts_settings/font_color','#226767');   
        $font_size = $this->getConfig('charts_settings/font_size',14);
        $chart_color = $this->getConfig('charts_settings/color','#333');


    		$settings['columns_array'] = array("revenue" => array(__("Date"), __("Sales Earnings"),'{ role: "style" }'),
                                     "orders" => array(__("Date"), __("Total Orders"),'{ role: "style" }'),
                                     "items" => array(__("Date"), __("Total Purchased Items"),'{ role: "style" }'),
                                     "qty" => array(__("Date"), __("Total QTY Ordered"),'{ role: "style" }')
                                    );

    		$settings['heading_title'] = __("Earnings");
    		$settings['chart_color'] = $chart_color;
    	  $settings['prefix'] = $this->_localeCurrency->getCurrency($this->_storeManager->getStore()->getCurrentCurrencyCode())->getSymbol();  
    		$settings['haxis'] = '{title: "'.__("Date").'", titleTextStyle: {color: "#333"}, textStyle: {color: "'.$font_color.'", fontSize:'.$font_size.'}}';
    		$settings['vaxis'] = '{minValue: 0, format: "'.$settings['prefix'].'#", textStyle: {color: "'.$font_color.'", fontSize:'.$font_size.'}}';
    		$settings['chart_class'] = '';
    		$settings['chart_width'] = '300px';
    		$settings['chart_height'] = '300px';

    		$chart_items_1 = $this->_getChartData($settings);
        $chart_items_2 = $this->_getChartData($settings, "orders_count");
        $chart_items_3 = $this->_getChartData($settings, "total_item_count");
        $chart_items_4 = $this->_getChartData($settings, "total_qty_ordered");

        $data['chart_items_array'] = array(array("data" => $chart_items_1, 
                                          "id"    => "revenue",
                                          'isDefault' => true,
                                          "prefix"=> $settings['prefix'],
                                          "label" => __("Revenue")
                                          ),
                                    array("data"  => $chart_items_2, 
                                          "id"    => "orders",
                                          "prefix"=> "",
                                          "label" => __("Orders")
                                          ),
                                    array("data"  => $chart_items_3,
                                          "id"    => "items",
                                          "prefix"=> "",
                                          "label" => __("Items")
                                          ),
                                    array("data"  => $chart_items_4,
                                          "id"    => "qty",
                                          "prefix"=> "",
                                          "label" => __("QTY")
                                          )
        ); 
    		$initChartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Area')->initGoogleChart($settings);
    		$chartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Area')->buildChart($data, $settings);
 
        $this->setChartItems($data['chart_items_array']);
    		$this->setInitChart($initChartHtml);
    		$this->setReportChart($chartHtml);
    		$this->setSettings($settings);
      	return parent::_toHtml();
    }

    protected function _getChartData( $settings = array(), $chart_field = "total_revenue_amount") {
    	 $filterData = $this->getFilterData();
        $filter_year = $filterData->getData('filter_year', null);
        $filter_month = $filterData->getData('filter_month', null);
        $filter_day = $filterData->getData('filter_day', null);
        $chart_color = isset($settings['chart_color'])?$settings['chart_color']: '#333';

        $reports = array();
        $tmp = array();

        $result_collection = $this->_registry->registry('report_collection'); //Get result report collection
        $reports = $this->_registry->registry('report_items'); //Get result report items array
        if($result_collection && !$reports) {
        	$reports = $result_collection->getArrayItems("period");
        	if($reports){
            $this->_registry->register('report_items', $reports);
          }
        }

        //Init chart item data
    	if($filter_year && !$filter_month) {
            for($i =1; $i <= 12; $i++) {
              $datefield = date('M', mktime(0, 0, 0, $i, 10));
              $item_subtotal = 0;
              if(isset($reports[$i])) {
                $item_subtotal = $reports[$i]->getData($chart_field);
              }
              $tmp[] = "['".$datefield."', ".$item_subtotal.", 'color:".$chart_color."']";
            }
          } elseif($filter_year && $filter_month && !$filter_day) {
            $number_days = date('t', mktime(0, 0, 0, $filter_month, 1, $filter_year));
            for($i = 1; $i <= $number_days; $i++) { 
              $datefield = $i;
              $item_subtotal = 0;
              if(isset($reports[$i])) {
                $item_subtotal = $reports[$i]->getData($chart_field);
              }
              $tmp[] = "['".$datefield."', ".$item_subtotal.", 'color:".$chart_color."']";
            }
          } elseif($filter_year && $filter_month && $filter_day) {
            for($i = 0; $i <= 23; $i++) {
              $datefield = ((strlen($i) < 2) ? "0{$i}" : $i).":00";
              $item_subtotal = 0;
              if(isset($reports[$i])) {
                $item_subtotal = $reports[$i]->getData($chart_field);
              }
              $tmp[] = "['".$datefield."', ".$item_subtotal.", 'color:".$chart_color."']";
            }
          } else {
          	if($reports) {
          		foreach($reports as $key=>$item) {
	              if($item) {
	                $tmp[] = "['".$key."', ".$item->getData($chart_field).", 'color:".$chart_color."']";
	              }
	            }
          	}
        }
        return $tmp;
    }
}