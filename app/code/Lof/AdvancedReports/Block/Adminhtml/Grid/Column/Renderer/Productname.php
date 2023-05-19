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
namespace Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer; 

use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

/**
 * Adminhtml grid item renderer date
 */
class Productname extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    { 
        $filterData = $this->getColumn()->getFilterData();
        $filter_from = $filterData->getData('filter_from', null);
        $filter_to = $filterData->getData('filter_to', null);
        $sku   = $row->getSku(); 
        $product_id = $row->getProductId();
        $product_name = $row->getData($this->getColumn()->getIndex()); 
        if(!$filter_from && !$filter_to) {
            $cur_month = date("m");
            $cur_year = date("Y");
            $filter_from = $cur_month."/01/".$cur_year;
            $filter_to = date("m/d/Y");
        }
        $product_type = $row->getProductType();
        $filterData = array();
        $filterData[] = 'product_id='.$product_id;
        $filterData[] = 'product_sku='.$sku;
        $filterData[] = 'name='.$product_name;
        $filterData[] = 'filter_from='.$filter_from;
        $filterData[] = 'filter_to='.$filter_to;
        $filterData[] = 'show_order_statuses=0';
        $filterData[] = 'report_field=main_table.created_at';

        $filter = implode("&",$filterData);
        $filter = base64_encode($filter);
        if($product_type == 'configurable'){
            return sprintf('<a href="%s" title="%s">%s</a>',
                $this->getUrl('*/advancedreports_products/productsconfiguration/', array('loffilter' => $filter)),
                __('Show Product Report'), $product_name
            );
        }else{
            return sprintf('<a href="%s" title="%s">%s</a>',
                $this->getUrl('*/advancedreports_sales/product/', array('loffilter' => $filter)),
                __('Show Product Report'), $product_name
            );
        }

    }
}
