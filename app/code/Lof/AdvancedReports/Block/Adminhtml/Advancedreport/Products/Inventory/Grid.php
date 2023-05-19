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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Products\Inventory;

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
        $this->setId('inventoryGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);

    }
 /**
     * {@inheritdoc}
     */
 public function getResourceCollectionName()
 {
    return 'Lof\AdvancedReports\Model\ResourceModel\Products\Collection';
}

protected function _getStore()
{
    $storeId = (int)$this->getRequest()->getParam('store', 0);
    return $this->_storeManager->getStore($storeId);
}

protected function _prepareColumns()
{
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

    $this->addColumn('total_qty', array(
        'header'    => __('Purchased Qty'),
        'align'     => 'right',
        'filter'    => false,
        'index'     => 'total_qty',
        'type'      => 'number',
        'total'     => 'sum',
    ));

    $this->addColumn('total_revenue_amount', array(
        'header'    => __('Revenue'),
        'align'     => 'right',
        'filter'    => false,
        'currency_code' => $store->getBaseCurrency()->getCode(),
        'index'     => 'total_revenue_amount',
        'type'      => 'currency',
        'total'     => 'sum',
    ));

    $this->addColumn('total_tax_amount', array(
        'header'    => __('Tax'),
        'align'     => 'right',
        'filter'    => false,
        'currency_code' => $store->getBaseCurrency()->getCode(),
        'index'     => 'total_tax_amount',
        'type'      => 'currency',
        'total'     => 'sum',
    ));

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
    $this->addExportType('*/*/exportInventoryCsv', __('CSV'));
    $this->addExportType('*/*/exportInventoryExcel', __('Excel XML'));

    return parent::_prepareColumns();
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

    $category_ids   = $filterData->getData('category_ids');

    if( $category_ids ){
        $category_id = explode(',', $category_ids);
        $collection->addCategoriesFilter(['in' => $category_id]);
    }

    $product_sku    = $filterData->getData('product_sku');

    if( $product_sku ){
        $collection->addAttributeToFilter('sku', array('eq' => $product_sku));
    }

    $qty_from       = $filterData->getData('qty_from');
    $qty_to         = $filterData->getData('qty_to');

    if( $qty_from && $qty_to ){
        $collection->getSelect()->where('at_qty.qty BETWEEN ' . $qty_from . ' AND ' . $qty_to. '');
    }
    $currencyCode       = $this->getCurrentCurrencyCode(null);
    $resourceProductCollection = $this->_objectManager->create($this->getResourceCollectionName())
    ->setOrderRate($currencyCode)
    ->prepareInventoryCollection()
    ->setMainTableId("product_id")
    ->setDateColumnFilter('created_at')
    ->addStoreFilter($storeIds);

    $order_statuses = $filterData->getData('order_statuses');
    $resourceProductCollection->addOrderStatusFilter($filterData->getData('order_statuses'));
    $resourceProductCollection->getSelect()
    ->group('product_id');
    $resourceProductCollection->applyCustomFilter();

    $purchased_product_select = $resourceProductCollection->getSelect();

    if( is_null($order_statuses) || !$order_statuses ) {
        $collection->getSelect()->joinLeft(array('payment'=>$purchased_product_select),'e.entity_id=payment.product_id');
    }else{
        $collection->getSelect()->join(array('payment'=>$purchased_product_select),'e.entity_id=payment.product_id');
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
