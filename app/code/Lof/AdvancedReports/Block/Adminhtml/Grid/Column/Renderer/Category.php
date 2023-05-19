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
class Category extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected $_categoryFactory;
    public function __construct(
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory, 
        \Magento\Backend\Block\Context $context, 
        array $data = [])
    {
        parent::__construct($context, $data); 
        $this->_categoryFactory = $categoryFactory;
    }
    public function render(\Magento\Framework\DataObject $row)
    {

        $category_id = $row->getData($this->getColumn()->getIndex());
        $category = $this->_categoryFactory->create()->load($category_id); 
        return '<strong title="'.__('Cat ID: ').$category_id.'">'.$category->getName().'</strong>';
    }
}
