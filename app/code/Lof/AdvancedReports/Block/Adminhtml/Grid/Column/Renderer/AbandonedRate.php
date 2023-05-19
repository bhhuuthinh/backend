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
 
/**
 * Adminhtml grid item renderer date
 */
class AbandonedRate extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $total_cart = $row->getData("total_cart");
        $total_abandoned_cart = $row->getData("total_abandoned_cart");
        $percent = 0;
        if($total_cart && (int)$total_cart > 0) {
            $percent = round(((int)$total_abandoned_cart/(int)$total_cart)*100, 2);
        }
        $max_width = 50;
        $margin_width = ($percent/100)*(int)$max_width;
        return '<span class="report-profit-margin-box abandoned-cart-percent" style="width: '.$margin_width.'px"></span>'.$percent.'%';
    }
}
