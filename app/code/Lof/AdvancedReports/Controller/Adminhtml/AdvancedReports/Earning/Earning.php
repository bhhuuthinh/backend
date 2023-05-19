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
namespace Lof\AdvancedReports\Controller\Adminhtml\AdvancedReports\Earning; 
class Earning extends \Lof\AdvancedReports\Controller\Adminhtml\AdvancedReports\Earning
{
    /**
     * Shipping report action
     *
     * @return void
     */
    public function execute()
    {    
        //$this->_showLastExecutionTime(Flag::REPORT_SHIPPING_FLAG_CODE, 'earning');

        $this->_initAction()->_setActiveMenu(
            'Lof_AdvancedReports::earningreport'
        )->_addBreadcrumb(
            __('Earning'),
            __('Earning')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Earning Report'));  
        
        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_advancedreport_earning.grid');
        $breadcrumbsBlock = $this->_view->getLayout()->getBlock('report.breadcrumbs');  
        $chartBlock = $this->_view->getLayout()->getBlock('report.chart');
        $bestsellersBlock = $this->_view->getLayout()->getBlock('report.sales.bestsellers');
        $topcountriesBlock = $this->_view->getLayout()->getBlock('report.sales.topcountries');
        $statisticsBlock = $this->_view->getLayout()->getBlock('report.topbar');
        $paymentsBlock = $this->_view->getLayout()->getBlock('report.sales.payments');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');
        $bottomBlock = $this->_view->getLayout()->getBlock('report.content.bottom');  
        
        $this->_initReportAction([$gridBlock,
                                 $filterFormBlock, 
                                 $breadcrumbsBlock, 
                                 $chartBlock,
                                 $bottomBlock,
                                 $bestsellersBlock,
                                 $topcountriesBlock,
                                 $paymentsBlock,
                                 $statisticsBlock]); 
        $this->_view->renderLayout();
    }

    /**
     * Report action init operations
     *
     * @param array|Varien_Object $blocks
     * @return Mage_Adminhtml_Controller_Report_Abstract
     */
    public function _initReportAction($blocks, $report_type = "")
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }
        $order = array();
        $sort_by = $this->getRequest()->getParam('sort');
        $dir = $this->getRequest()->getParam('dir');

        $requestData = $this->_objectManager->get(
            'Magento\Backend\Helper\Data'
        )->prepareFilterString(
            $this->getRequest()->getParam('loffilter')
        );  
        // $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('vesfilter'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        if(!isset($requestData['filter_year']) || !$requestData['filter_year']) {
          $requestData['filter_year'] = $this->getRequest()->getParam('filter_year');
        }
        if(!isset($requestData['filter_month']) || !$requestData['filter_month']) {
          $requestData['filter_month'] = $this->getRequest()->getParam('filter_month');
        }
        if(!isset($requestData['filter_day']) || !$requestData['filter_day']) {
          $requestData['filter_day'] = $this->getRequest()->getParam('filter_day');
        }
        if(!isset($requestData['show_order_statuses'])) {
          $requestData['show_order_statuses'] = $this->getRequest()->getParam('show_order_statuses');
        }
        if($requestData['show_order_statuses'] == NULL && $requestData['show_order_statuses'] == "") {
          $requestData['show_order_statuses'] = 1;
        }

        if($this->getRequest()->getParam('current')) {
            $requestData['filter_year'] = date("Y");
            $requestData['filter_month'] = date("m");
            $requestData['filter_day'] = '';
        }
        if(!isset($requestData['order_statuses'])) {
            $requestData['order_statuses'] =  'complete';
        }
        if($requestData['show_order_statuses'] == 0) {
            $requestData['order_statuses'] = "";
        }
        $params = new \Magento\Framework\DataObject();
        // $params = new Varien_Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        } 

        $period_type = $this->_getPeriodType($requestData);
                
        foreach ($blocks as $block) { 
            if ($block) {
                $block->setPeriodType($period_type);
                $block->setFilterData($params);
                $block->setCulumnOrder($sort_by);
                $block->setOrderDir($dir);
            }
        }

        return $this;
    }
 
    protected function _getPeriodType ($requestData = array()) {
        $period_type = "day";
        if($requestData['filter_year'] && $requestData['filter_month'] && $requestData['filter_day']) {
          $period_type = "hour";
        }elseif($requestData['filter_year'] && $requestData['filter_month'] && !$requestData['filter_day']) {
          $period_type = "day";
        }elseif($requestData['filter_year'] && !$requestData['filter_month'] && !$requestData['filter_day']) {
          $period_type = "month";
        }elseif(!$requestData['filter_year'] && !$requestData['filter_month'] && !$requestData['filter_day']) {
          $period_type = "year";
        }
        return $period_type;
    } 
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        $this->initVerification();
        if (!$this->getData('is_valid') && !$this->getData('local_valid')) {
            return false;
        }
        return $this->_authorization->isAllowed('Lof_AdvancedReports::earningreport_view');
    }
    

}
