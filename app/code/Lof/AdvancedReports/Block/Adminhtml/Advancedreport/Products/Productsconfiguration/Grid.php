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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Products\Productsconfiguration;

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
        $this->setId('productsGrid');
        $this->setUseAjax(false);  
    } 
    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Products\Collection';
    }
    protected function _prepareColumns()
    {      
        $this->addColumn('name', array(
            'header'    =>  __('Product Name'),
            'width'     => '215px',
            'index'     => 'name',
            'type'      => 'text',
            'filter'    => false,
            'filter_data'   => $this->getFilterData(),
            'renderer'      => 'Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer\Productname',
            'totals_label'  => __('Total'),
            'html_decorators' => array('nobr')
        ));
        $this->addColumn('sku', array(
            'header'    => __('Product SKU'),
            'type'      => 'text',
            'filter'    => false,
            'index'     =>'sku'
        )); 

        $this->addColumn('total_qty', array(
            'header'    => __('Quantity'),
            'align'     => 'right',
            'filter'    => false,
            'index'     => 'total_qty',
            'type'      => 'number',
            'total'     => 'sum',
        ));

        $this->addColumn('orders_count', array(
            'header'    => __('Unique Purchases'),
            'align'     => 'right',
            'filter'    => false,
            'index'     => 'orders_count',
            'type'      => 'number',
            'total'     => 'sum',
        ));
 

        $this->addExportType('*/*/exportProductsReportCsv', __('CSV'));
        $this->addExportType('*/*/exportProductsReportExcel', __('Excel XML'));
        // }

        // $this->initExportActions();

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
        $this->setDefaultSort("orders_count");
        $this->setDefaultDir("DESC"); 
        $storeIds = $this->_getStoreIds();   
        $childProducts = $this->_productConfigurable->getChildrenIds($filterData->getData('product_id', null));  
        $resourceCollection = $this->_objectManager->create($this->getResourceCollectionName())
        ->prepareProductsConfigCollection()
        ->setMainTableId("product_id")
        ->setPeriodType($this->getPeriodType())
        ->setDateColumnFilter($this->_columnDate)
        ->addDateFromFilter($filterData->getData('filter_from', null))
        ->addDateToFilter($filterData->getData('filter_to', null))
        // ->addProductNameFilter($filterData->getData('name', null))
        ->addProductSkuFilter($filterData->getData('sku', null)) 
        ->addStoreFilter($storeIds)
        ->setAggregatedColumns($this->_getAggregatedColumns());
    
        if(sizeof($childProducts) > 0){
            $resourceCollection->addProductIdFilter();
        }
        $this->_addOrderStatusFilter($resourceCollection, $filterData);
        $this->_addCustomFilter($resourceCollection, $filterData);
        $resourceCollection->getSelect()
        ->group('product_id')
        ->order(new \Zend_Db_Expr($this->getColumnOrder()." ".$this->getColumnDir()));

        $resourceCollection->applyCustomFilter();
        $resourceCollection->applyProductIdFilter();  
        $resourceCollection->setPageSize((int) $this->getParam($this->getVarNameLimit(), $limit));
        $resourceCollection->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage)); 
        $order_filter = $this->getParam($this->getVarNameFilter(), null); 
        if ($filterData->getData('show_empty_rows', false)) {
            $this->_reportsData->prepareIntervalsCollection(
                $this->getCollection(),
                $filterData->getData('year', null),
                $filterData->getData('month', null),
                $filterData->getData('day', null)
            );
        } 
        $this->setCollection($resourceCollection); 
        if(!$this->_registry->registry('report_collection')) {
            $this->_registry->register('report_collection', $resourceCollection);
        } 

        $this->_prepareTotals('price,total_qty,total_canceled,total_qty_invoiced,total_qty_refunded,total_refunded_amount,total_tax_amount,total_discount_amount,total_revenue_amount,orders_count,avg_price,avg_qty,avg_revenue,avg_refunded,avg_discount_amount,avg_tax_amount,avg_refunded_amount'); //Add this Line with all the columns you want to have in totals bar

        return parent::_prepareCollection();
    } 

}