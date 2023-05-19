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

class ListPeriod implements \Magento\Framework\Option\ArrayInterface {
    public function toOptionArray() {
        return array(
            array(
                'value' => 'today',
                'label' => __('Today'),
            ),
            array(
                'value' => 'yesterday',
                'label' => __('Yesterday'),
            ),
            array(
                'value' => 'last_7_days',
                'label' => __('Last 7 Days'),
            ),
            array(
                'value' => 'last_30_days',
                'label' => __('Last 30 Days'),
            ),
            array(
                'value' => 'last_week',
                'label' => __('Last week (Sun - Sat)'),
            ),
            array(
                'value' => 'last_business_week',
                'label' => __('Last business week (Mon - Fri)'),
            ),
            array(
                'value' => 'this_month',
                'label' => __('This Month'),
            ),
            array(
                'value' => 'last_month',
                'label' => __('Last month'),
            ),
        );
    }
}