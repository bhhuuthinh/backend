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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Customer\Customernotorder;

class Grid extends \Lof\AdvancedReports\Block\Adminhtml\Grid\AbstractGrid
{

    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = 'period';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';
    protected $_resource_grid_collection = null;

    public function _construct()
    {
        parent::_construct();
        $this->setId('customernotorderGrid');
        $this->setUseAjax(false);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareColumns()
    {    
         $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));

        $this->addColumn('name', array(
            'header'    => __('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => __('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));
        $groups  = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Group\Collection')
        ->addFieldToFilter('customer_group_id', array('gt'=> 0))
        ->load()
        ->toOptionHash();
        
        // $groups = Mage::getResourceModel('customer/group_collection')
        //     ->addFieldToFilter('customer_group_id', array('gt'=> 0))
        //     ->load()
        //     ->toOptionHash();

        $this->addColumn('group', array(
            'header'    => __('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('telephone', array(
            'header'    => __('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header'    => __('ZIP'),
            'width'     => '90',
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => __('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => __('State/Province'),
            'width'     => '100',
            'index'     => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header'    => __('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => __('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => $this->_systemStore->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }


        $this->addExportType('*/*/exportCustomernotorderCsv', __('CSV'));
        $this->addExportType('*/*/exportCustomernotorderExcel', __('Excel XML')); 

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
        $this->setDefaultSort("total_qty_ordered");
        $this->setDefaultDir("DESC");
        
        $storeIds = $this->_getStoreIds(); 
        $resourceCollection = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');  
        $resourceCollection = $this->_applyStoresFilterToSelect($resourceCollection); 
        //Order Collection
        $resourceOrderCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Customer\Order\Collection')
           ->prepareListOrderCustomersCollection()
            ->setDateColumnFilter('created_at')
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($storeIds);

        $resourceOrderCollection->addOrderStatusFilter($filterData->getData('order_statuses'));

        $resourceOrderCollection->getSelect()
                            ->group('customer_id'); 
        $resourceOrderCollection->applyCustomFilter();
        $order_select = $resourceOrderCollection->getSelect();
        //End Order Collection

        if ($order_select) {
            $resourceCollection->getSelect()->where("e.entity_id NOT IN (?)", $order_select);
        }  
        $resourceCollection->setPageSize((int) $this->getParam($this->getVarNameLimit(), $limit));
        $resourceCollection->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage)); 
        $this->setCollection($resourceCollection); 
        if(!$this->_registry->registry('report_collection')) {
            $this->_registry->register('report_collection', $resourceCollection);
        }   
        return parent::_prepareCollection();  
    }  
    /**
     * Apply stores filter to select object
     *
     * @param Zend_Db_Select $select
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    protected function _applyStoresFilterToSelect($resourceCollection )
    {
        $nullCheck = false;
        $storeIds  = $this->_getStoreIds();
        $select = $resourceCollection->getSelect();

        if($storeIds) {
            if (!is_array($storeIds)) {
                $storeIds = array($storeIds);
            }

            $storeIds = array_unique($storeIds);

            if ($index = array_search(null, $storeIds)) {
                unset($storeIds[$index]);
                $nullCheck = true;
            }

            $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

            if ($nullCheck) {
                $select->where('e.store_id IN(?) OR e.store_id IS NULL', $storeIds);
            } else {
                $select->where('e.store_id IN(?)', $storeIds);
            }
        }
        return $resourceCollection;
    }

}