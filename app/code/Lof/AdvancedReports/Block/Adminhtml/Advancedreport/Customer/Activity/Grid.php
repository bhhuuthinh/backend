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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Customer\Activity;

class Grid extends \Lof\AdvancedReports\Block\Adminhtml\Grid\AbstractGrid
{

    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = '';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';
    protected $_resource_grid_collection = null;
    protected $_scopeconfig;
    public function _construct()
    {  
        parent::_construct(); 
        $this->setCountTotals(true);
        $this->setFilterVisibility(true);
        $this->setPagerVisibility(true);
        $this->setId('detailedGrid');
        $this->setUseAjax(false);
        $this->setDefaultSort("created_at");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('customer_filter'); 
    } 
    protected function _prepareColumns()
    {      
        $filterData = $this->getFilterData();

        $this->addColumn('period', array(
            'header'        => __('Period'),
            'index'         => 'period',
            'width'         => 100,
            'filter_data'   => $this->getFilterData(),
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\Dateperiod',
            'totals_label'  =>  __('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('new_accounts_count', array(
            'header'    => __('New Accounts'),
            'index'     => 'new_accounts_count',
            'type'      => 'number',
            'total'     => 'sum',
        ));

        $this->addColumn('orders_count', array(
            'header'    => __('Orders'),
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
        ));

        $this->addColumn('reviews_count', array(
            'header'    => __('Reviews'),
            'index'     => 'reviews_count',
            'type'      => 'number',
            'total'     => 'sum',
        )); 
        $this->addExportType('*/*/exportCustomerActivityCsv', __('CSV'));
        $this->addExportType('*/*/exportCustomerActivityExcel', __('Excel XML')); 

        return parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        

        $filterData = $this->getFilterData(); 
        $report_type = $this->getReportType();
        $limit = $filterData->getData("limit", null);
        if(!$limit) {
            $limit = $this->_defaultLimit;
        }
        $report_field = $filterData->getData("report_field", null);
        $report_field = $report_field?$report_field: "main_table.created_at";
        $this->setCulumnDate($report_field);
        $this->setDefaultSort("period");
        $this->setDefaultDir("ASC"); 
        $storeIds = $this->_getStoreIds();     

        $order = $this->getColumnOrder();
        if("month" == $this->getPeriodType()){
            $order = "main_table.created_at";
        }
        $resourceCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Customer\Collection')
            ->prepareCustomerActivityCollection()
            ->setDateColumnFilter($this->_columnDate)
            ->setPeriodType($this->getPeriodType())
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns());

            
        $this->_addCustomFilter($resourceCollection, $filterData);
         
        $resourceCollection->getSelect()
                            ->group('period')
                            ->order(new \Zend_Db_Expr($order." ".$this->getColumnDir()));

        $resourceCollection->applyCustomFilterNew(); 
        //Order Collection
        $resourceOrderCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Customer\Order\Collection')
            ->prepareCustomerOrderCollection()
            ->setDateColumnFilter($this->_columnDate)
            ->setPeriodType($this->getPeriodType())
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns());

        $resourceOrderCollection->addOrderStatusFilter($filterData->getData('order_statuses'));

        $resourceOrderCollection->getSelect();

        $resourceOrderCollection->applyCustomFilter();
        $order_select = $resourceOrderCollection->getSelect(); 
        //End Order Collection
        
        //Review Collection
        $resourceReviewCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Customer\Review\Collection')
            ->prepareCustomerReviewCollection()
            ->setPeriodType($this->getPeriodType())
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns());

        $resourceReviewCollection->getSelect()
                            ->group('period');

        $resourceReviewCollection->applyCustomFilter();
        $review_select = $resourceReviewCollection->getSelect();
        //End Review Collection



        $resourceCollection->setPageSize((int) $this->getParam($this->getVarNameLimit(), $limit));
        $resourceCollection->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));

        $order_filter = $this->getParam($this->getVarNameFilter(), null);


        if($limit) {
            $resourceCollection->setPageSize((int) $this->getParam($this->getVarNameLimit(), $limit));
            $resourceCollection->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));
        }

        $resourceCollection->joinCustomerOrders($order_select);
        $resourceCollection->joinCustomerReviews($review_select); 

        if ($this->getCountSubTotals()) {
            $this->getSubTotals();
        }

        if (!$this->getTotals()) {
            $totalsCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Customer\Collection')
            ->prepareCustomerActivityCollection()
            ->setDateColumnFilter($this->_columnDate)
            ->setPeriodType($this->getPeriodType())
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($storeIds)
            ->setAggregatedColumns($this->_getAggregatedColumns())
            ->isTotals(true);

            //$this->_addOrderStatusFilter($totalsCollection, $filterData);
            $this->_addCustomFilter($totalsCollection, $filterData);

            $totalsCollection->getSelect()
                            ->group('period')
                            ->order(new \Zend_Db_Expr($order." ".$this->getColumnDir()));

            $totalsCollection->applyCustomFilterNew();

            foreach ($totalsCollection as $item) {
                $this->setTotals($item);
                break;
            }
        }
 
        $this->setCollection($resourceCollection); 
        if(!$this->_registry->registry('report_collection')) {
            $this->_registry->register('report_collection', $resourceCollection);
        }  

        $this->_prepareTotals('orders_count,new_accounts_count,reviews_count'); //Add this Line with all the columns you want to have in totals bar
        
        return parent::_prepareCollection();  
    }


    /**
     * Helper function to do after load modifications
     *
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _preparePage()
    {
        $this->getCollection()->setPageSize((int) $this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        $this->getCollection()->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/activity', array('_current'=>true));
    }

}