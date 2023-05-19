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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport;

class Breadcrumb extends \Magento\Backend\Block\Template
{
    protected $_breadcrumbs = array();  

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,    
        array $data = []
    ) { 
        parent::__construct($context, $data);   
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */

    protected function _prepareLayout()
    {         
        return parent::_prepareLayout();
    } 

    public function getBreadcrumbs() { 
        if(!$this->_breadcrumbs) { 
            $breadcrumbs = array();   
            $filterData = $this->getFilterData(); 
            $filter_year = $filterData->getData('filter_year', null); 
            $filter_month = $filterData->getData('filter_month', null);
            $filter_day = $filterData->getData('filter_day', null);

            $breadcrumbs[] = array(
                    'text'      => '<strong>'.__("Sales Earnings").'</strong>',
                    'separator' => false
                );

            if($filter_year) {
                $breadcrumbs[] = array(
                    'text'      => __("All Time"),
                    'href'      => $this->getBreadcrumbLink(),
                    'separator' => ' / '
                );
            }

            if($filter_month) {
                $breadcrumbs[] = array(
                    'text'      => $filter_year,
                    'href'      => $this->getBreadcrumbLink(array('filter_year='.$filter_year)),
                    'separator' => ' / '
                );
            }


            if($filter_day) {
                $breadcrumbs[] = array(
                    'text'      => date('F', mktime(0, 0, 0, $filter_month, 10)),
                    'href'      => $this->getBreadcrumbLink(array('filter_year='.$filter_year, 'filter_month='.$filter_month)),
                    'separator' => ' / '
                );
            }


            if(!$filter_year) {
                $breadcrumbs[] = array(
                    'text'      => '<strong>'.__("All Time").'</strong>',
                    'separator' => ' / '
                );
            } elseif($filter_year && !$filter_month) {
                $breadcrumbs[] = array(
                    'text'      => '<strong>'.$filter_year.'</strong>',
                    'separator' => ' / '
                );
            } elseif($filter_year && $filter_month && !$filter_day) {
                $breadcrumbs[] = array(
                    'text'      => '<strong>'.date('F', mktime(0, 0, 0, $filter_month, 10)).'</strong>',
                    'separator' => ' / '
                );
            } elseif($filter_year && $filter_month && $filter_day) {
                $week_day_name = $this->_getWeekDay($filter_day, $filter_month, $filter_year);
                $breadcrumbs[] = array(
                    'text'      => '<strong>'.$week_day_name.", ".date('F', mktime(0, 0, 0, $filter_month, 10))." ".$filter_day.'</strong>',
                    'separator' => ' / '
                ); 
            }
           
            $this->_breadcrumbs = $breadcrumbs;
        }
        return $this->_breadcrumbs;
    }

    protected function _getWeekDay($day,$month,$year){
        return date("l",strtotime($year.'-'.$month.'-'.$day));
    }

    public function getBreadcrumbLink($filter = array()) {
        if($filter) {
            $filter = implode("&",$filter);
            $filter = base64_encode($filter);
            return $this->getUrl('*/*/earning', array('loffilter'=>$filter));
        }
        return $this->getUrl('*/*/earning');
    }
}
