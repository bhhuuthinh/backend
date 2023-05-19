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
class Country extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    
    protected $_localList;
    public function __construct(
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Backend\Block\Context $context, 
        array $data = [])
    {
        parent::__construct($context, $data); 
        $this->_localList = $localeLists;
    }
    public function render(\Magento\Framework\DataObject $row)
    { 
        $country_code = $row->getData($this->getColumn()->getIndex()); 
        $country_name = $this->_localList->getCountryTranslation($country_code);
        $cell_value = ($country_name?$country_name:$country_code);
        return '<strong>'.$cell_value.'</strong>';
    }
}
