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

class Calculator extends \Magento\Backend\Block\Template
{ 
    protected $_helperData;
    protected $_registry = null; 
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,     
        \Lof\AdvancedReports\Helper\Data $helperData, 
        \Magento\Framework\Registry $registry, 
        array $data = []
        )
    { 
        $this->_helperData = $helperData; 
        $this->_registry = $registry; 
        parent::__construct($context, $data);     
        
    } 

    public function _toHtml() {
        $result_collection = $this->_registry->registry('report_collection'); 
        if(!$result_collection || (0 >= $result_collection->getSize()))
        return "";

        $this->initCalculator();

        return parent::_toHtml();
    }

    public function initCalculator($show_by = "total_profit_amount") {
        $result_collection = $this->_registry->registry('report_collection'); 
        $reports = $this->_registry->registry('report_items');
        if($result_collection && !$reports) {
            $reports = $result_collection->getArrayItems("method");
            if($reports){
            $this->_registry->register('report_items', $reports);
          }
        }
        /*
        $payment_profit = array();
        if($reports) {
            foreach($reports as $key=>$item) {
              if($item) {
                $paymentTitle = Mage::getStoreConfig('payment/'.$key.'/title');
                $payment_profit[$paymentTitle] = $item;
              }
            }
        }
        */

        $this->setListPayments($reports);
        return $this;
    }
    
    
}