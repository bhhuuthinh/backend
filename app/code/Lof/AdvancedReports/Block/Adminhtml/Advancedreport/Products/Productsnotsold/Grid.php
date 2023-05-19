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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Products\Productsnotsold;

use Magento\Store\Model\Store;
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
        $this->setId('productnotsoldGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('product_filter');
    }

    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    protected function _prepareColumns()
    {

        $groups  = $this->_objectManager->create('Magento\Customer\Model\ResourceModel\Group\Collection')
        ->addFieldToFilter('customer_group_id', array('gt'=> 0))
        ->load()
        ->toOptionHash();

        $this->addColumn('entity_id',
            array(
                'header'=>  __('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> __('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> __('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'=>  __('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => $this->_objectManager->create('\Magento\Catalog\Model\Product\Type')->getOptionArray(),
        ));

        $sets = $this->_setsFactory->create()->setEntityTypeFilter(
            $this->_productFactory->create()->getResource()->getTypeId()
        )->load()->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => __('Attribute Set'),
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
                'header_css_class' => 'col-attr-name',
                'column_css_class' => 'col-attr-name'
            ]
        );

        $this->addColumn('sku',
            array(
                'header'=> __('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $store = $this->_getStore();

        $filterData = $this->getFilterData();
        $currencyCodeParam = $filterData->getData('currency_code') ?: null;
        $currencyCode      = $this->getCurrentCurrencyCode($currencyCodeParam);
        $rate              = $this->getRate($currencyCode) ?: 1;
        $this->addColumn('price',
            array(
                'header'        =>  __('Price'),
                'type'          => 'price',
                'currency_code' => $currencyCode,
                'index'         => 'price',
                'rate'          => $rate
        ));

        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header'=> __('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
            ));
        }

        $this->addColumn('visibility',
            array(
                'header'=>  __('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => $this->_objectManager->create('\Magento\Catalog\Model\Product\Visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'=> __('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => $this->_objectManager->create('\Magento\Catalog\Model\Product\Attribute\Source\Status')->getOptionArray(),
        ));
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                [
                    'header' => __('Websites'),
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    'options' => $this->_websiteFactory->create()->getCollection()->toOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }

        $this->addColumn('action',
            array(
                'header'    => __('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' =>  __('Edit'),
                        'url'     => array(
                            'base'=>'catalog/product/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        // if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
        //     $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        // }
        $this->addExportType('*/*/exportProductsNotSoldCsv', __('CSV'));
        $this->addExportType('*/*/exportProductsNotSoldExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('catalog/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_objectManager->create('\Magento\Catalog\Model\Product\Attribute\Source\Status')->getOptionArray();

        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change Status'),
                'url' => $this->getUrl('catalog/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );

        if ($this->_authorization->isAllowed('Magento_Catalog::update_attributes')) {
            $this->getMassactionBlock()->addItem(
                'attributes',
                [
                    'label' => __('Update Attributes'),
                    'url' => $this->getUrl('catalog/product_action_attribute/edit', ['_current' => true])
                ]
            );
        }

        $this->_eventManager->dispatch('adminhtml_catalog_product_grid_prepare_massaction', ['block' => $this]);
        return $this;
    }
    protected function _prepareCollection()
    {
        $storeIds  = $this->_getStoreIds();
        $filterData = $this->getFilterData();
        $report_type = $this->getReportType();
        $store = $this->_getStore();
        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'attribute_set_id'
        )->addAttributeToSelect(
            'type_id'
        )->setStore(
            $store
        );

        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $collection->joinField(
                'qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
        }
        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                Store::DEFAULT_STORE_ID
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }


        //Purchased Products Collection
        $currencyCode       = $this->getCurrentCurrencyCode(null);
        $resourceProductCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Products\Order\Collection')
            ->setOrderRate($currencyCode)
            ->prepareListProductCollection()
            ->setDateColumnFilter('created_at')
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($storeIds);

        $resourceProductCollection->addOrderStatusFilter($filterData->getData('order_statuses'));

        $resourceProductCollection->getSelect()
                            ->group('product_id');

        $resourceProductCollection->applyCustomFilter();
        $purchased_product_select = $resourceProductCollection->getSelect();
        //Purchased Products Collection
        if ($purchased_product_select) {
            $collection->getSelect()->where("e.entity_id NOT IN (?)", $purchased_product_select);
        }
        $this->setCollection($collection);
        $this->getCollection()->addWebsiteNamesToResult();
        parent::_prepareCollection();

        return $this;
    }
        /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

}
