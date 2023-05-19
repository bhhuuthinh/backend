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
class ListFileFormat implements \Magento\Framework\Option\ArrayInterface {
    public function toOptionArray() {
        return array(
            array(
                'value' => 'csv',
                'label' => __('CSV'),
            ),
            array(
                'value' => 'xml',
                'label' => __('XML'),
            )
        );
    }
}