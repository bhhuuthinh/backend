<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lof_AdvancedReports
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\AdvancedReports\Model\Config\Source;

class Mobilereports implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return mixed
     */
    public function toOptionArray()
    {
        return [
            ['value' => "earning", "label" => __('Earning')],
            ['value' => "detailed", "label" => __('Order Detailed Report')],
            ['value' => "guestorders", "label" => __('Order By Guests Report')],
            ['value' => "abandoned", "label" => __('Abandoned Carts')],
            ['value' => "abandoneddetailed", "label" => __('Abandoned Detailed Carts')],
            ['value' => "itemsdetailed", "label" => __('Order Items Detailed Report')],
            ['value' => "activity", "label" => __('Customer Activity Report')],
            ['value' => "customersreport", "label" => __('Customer Report')],
            ['value' => "topcustomers", "label" => __('Top Customer Report')],
            ['value' => "productscustomer", "label" => __('Products Customer Report')],
            ['value' => "customerscity", "label" => __('Customers by City')],
            ['value' => "customerscountry", "label" => __('Customers by Country')],
            ['value' => "customernotorder", "label" => __('Customers Not Order')],
            ['value' => "productsreport", "label" => __('Products Report')],
            ['value' => "productsnotsold", "label" => __('Products Not Sold')],
            ['value' => "overview", "label" => __('Sales Overview')],
            ['value' => "statistics", "label" => __('Sales Statistics')],
            ['value' => "customergroup", "label" => __('Sales By Customer Group')],
            ['value' => "producttype", "label" => __('Sales Product Type')],
            ['value' => "hour", "label" => __('Sales by Hour')],
            ['value' => "dayofweek", "label" => __('Sales by Day Of Week')],
            ['value' => "product", "label" => __('Sales By Product')],
            ['value' => "category", "label" => __('Sales Category')],
            ['value' => "paymenttype", "label" => __('Sales By Payment Type')],
            ['value' => "country", "label" => __('Sales By Country')],
            ['value' => "region", "label" => __('Sales By Region/State')],
            ['value' => "zipcode", "label" => __('Sales By Zipcode')],
            ['value' => "coupon", "label" => __('Sales By Coupon')],
            ['value' => "manage_customer", "label" => __('Manage Customer')],
            ['value' => "manage_order", "label" => __('Manage Order')]
        ];
    }
}
