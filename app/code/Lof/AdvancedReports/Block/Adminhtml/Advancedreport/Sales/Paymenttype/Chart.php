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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Sales\Paymenttype;

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
        $result_collection = $this->_registry->registry('report_collection'); 
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return false;

        $this->initPaymentReportChart();

        return $this;
    }
    
    public function initPaymentReportChart() {
        $report_type = $this->getReportType();
        $filterData = $this->getFilterData();

        $settings = array();
        $data = array();

        $settings['columns_array'] = array("sales_total" => array(__("Payment Method"),__("Sales Total")),
                                     "orders" => array(__("Payment Method"),__("Orders")),
                                     "items" => array(__("Payment Method"),__("Sales Items")),
                                     "refund" => array(__("Payment Method"),__("Refunded")),
                                     "discount" => array(__("Payment Method"),__("Sales Discount")),
                                     "canceled" => array(__("Payment Method"),__("Canceled"))
                                    );

        $settings['heading_title'] = __("Sales By Payment Type");
        $settings['chart_class'] = '';
        $settings['chart_width'] = '300px';
        $settings['chart_height'] = '300px';
        $settings['prefix'] = $this->_localeCurrency->getCurrency($this->_storeManager->getStore()->getCurrentCurrencyCode())->getSymbol(); 

        $chart_items_1 = $this->_getPaymentReportChartData($settings, "total_income_amount");
        $chart_items_2 = $this->_getPaymentReportChartData($settings, "orders_count");
        $chart_items_3 = $this->_getPaymentReportChartData($settings, "total_qty_ordered");
        $chart_items_4 = $this->_getPaymentReportChartData($settings, "total_refunded_amount");
        $chart_items_5 = $this->_getPaymentReportChartData($settings, "total_discount_amount");
        $chart_items_6 = $this->_getPaymentReportChartData($settings, "total_canceled_amount");

        $data['chart_items_array'] = array(array("data" => $chart_items_1, 
                                          "id"    => "sales_total",
                                          'isDefault' => true,
                                          "prefix"=> $settings['prefix'],
                                          "label" => __("Sales Total")
                                          ),
                                    array("data"  => $chart_items_2, 
                                          "id"    => "orders",
                                          "prefix"=> "",
                                          "label" => __("Orders")
                                          ),
                                    array("data"  => $chart_items_3,
                                          "id"    => "items",
                                          "prefix"=> "",
                                          "label" => __("Sales Items")
                                          ),
                                    array("data"  => $chart_items_4,
                                          "id"    => "refund",
                                          "prefix"=> $settings['prefix'],
                                          "label" => __("Refunded")
                                          ),
                                    array("data"  => $chart_items_5,
                                          "id"    => "discount",
                                          "prefix"=> $settings['prefix'],
                                          "label" => __("Sales Discount")
                                          ),
                                     array("data"  => $chart_items_6,
                                          "id"    => "canceled",
                                          "prefix"=> $settings['prefix'],
                                          "label" => __("Canceled")
                                          )
        );

        $initChartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Pie')->initGoogleChart($settings);
      $chartHtml = $this->_objectManager->get('Lof\AdvancedReports\Helper\GoogleChart\Pie')->buildChart($data, $settings);

        $this->setChartItems($data['chart_items_array']);
        $this->setInitChart($initChartHtml);
        $this->setReportChart($chartHtml);
        $this->setSettings($settings);
    }
    protected function _getPaymentReportChartData( $settings = array(), $show_by = "total_paid_amount") {
        $filterData = $this->getFilterData();
        $reports = array();
        $tmp = array();

        $result_collection = $this->_registry->registry('report_collection'); 
        $reports = $this->_registry->registry('report_items');
        if($result_collection && !$reports) {
            $reports = $result_collection->getArrayItems("method");
            if($reports){
            $this->_registry->register('report_items', $reports);
          }
        }

        //Init chart item data
    
        if($reports) {
            foreach($reports as $key=>$item) {
              if($item) {
                $paymentTitle = $this->_storeManager->getStore()->getConfig('payment/'.$key.'/title');
                $tmp[] = "['".$paymentTitle."', ".(float)$item->getData($show_by)."]";
              }
            }

        }
        
        return $tmp;
    }
}