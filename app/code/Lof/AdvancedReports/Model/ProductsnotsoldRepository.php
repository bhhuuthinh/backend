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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\AdvancedReports\Model;
use Lof\AdvancedReports\Api\ProductsnotsoldInterface;
use Lof\AdvancedReports\Model\AbstractReport;
use Magento\Framework\Api\SortOrder;
use Magento\Store\Model\Store;

class ProductsnotsoldRepository extends AbstractReport implements ProductsnotsoldInterface
{
    protected $_limit = 10;
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnDate = 'main_table.created_at';
    protected $_defaultSort = 'entity_id';
    protected $_defaultDir = 'DESC';
    protected $_productFactory;
    protected $moduleManager;
    protected $_websiteFactory;
    protected $_setsFactory;

    public function __construct(   
        \Lof\AdvancedReports\Helper\Data $helperData, 
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Lof\AdvancedReports\Helper\Api\Datefield $helperDatefield, 
        \Lof\AdvancedReports\Api\Data\ProductsnotsolddataInterfaceFactory $searchResultsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
        )
    {
        $this->_websiteFactory = $websiteFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->_productFactory = $productFactory;
        $this->moduleManager = $moduleManager;
        $this->_setsFactory = $setsFactory;
        parent::__construct($helperData, $objectManager, $storeManager, $localeCurrency, $searchCriteriaBuilder, $localeLists, $helperDatefield);
    }
    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Lof\AdvancedReports\Model\ResourceModel\Products\Order\Collection';
    }

    /**
    * @return \Magento\Framework\DataObject
    */
    public function initFilterData($filter_field = []) {
        $requestData = [];
        $lofFilter = isset($filter_field['lofFilter'])?isset($filter_field['lofFilter']):null;
        $storeIds = isset($filter_field['storeIds'])?isset($filter_field['storeIds']):null;

        if($lofFilter) {
            $requestData = $this->_objectManager->get(
                'Magento\Backend\Helper\Data'
            )->prepareFilterString(
                $lofFilter
            );
        }

        $requestData['store_ids'] = $storeIds;

        if(!isset($requestData['report_field']) || !$requestData['report_field']) {
          $requestData['report_field'] = isset($filter_field['report_field'])?$filter_field['report_field']:$this->_columnDate;
        }
        if(!isset($requestData['filter_from']) || !$requestData['filter_from']) {
          $requestData['filter_from'] = isset($filter_field['filter_from'])?$filter_field['filter_from']:"";
        }
        if(!isset($requestData['filter_to']) || !$requestData['filter_to']) {
          $requestData['filter_to'] = isset($filter_field['filter_to'])?$filter_field['filter_to']:"";
        }

        if(!isset($requestData['name']) || !$requestData['name']) {
          $requestData['name'] = isset($filter_field['name'])?$filter_field['name']:"";
        }

        if(!isset($requestData['sku']) || !$requestData['v']) {
          $requestData['sku'] = isset($filter_field['sku'])?$filter_field['sku']:"";
        }
        
        if(!isset($requestData['group_by']) || !$requestData['group_by']) {
          $requestData['group_by'] = isset($filter_field['group_by'])?$filter_field['group_by']:"month";
        }
        if(!isset($requestData['show_actual_columns']) || !$requestData['show_actual_columns']) {
          $requestData['show_actual_columns'] = isset($filter_field['show_actual_columns'])?$filter_field['show_actual_columns']:0;
        }
        if(!isset($requestData['show_order_statuses']) || ($requestData['show_order_statuses'] == NULL && $requestData['show_order_statuses'] == "")) {
          $requestData['show_order_statuses'] = isset($filter_field['show_order_statuses'])?(int)$filter_field['show_order_statuses']:1;;
        }

        if(!isset($requestData['order_statuses'])) {
            $requestData['order_statuses'] =  isset($filter_field['order_statuses'])?$filter_field['order_statuses']:"complete";
        }
        if($requestData['show_order_statuses'] == 0) {
            $requestData['order_statuses'] = "";
        }

        $params = new \Magento\Framework\DataObject();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
        $this->setFilterData($params);

        return $params;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\AdvancedReports\Api\Data\ProductsnotsolddataInterface
     */
    public function getProducts(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
        $cur_month = date("m");
        $cur_year = date("Y");
        $filter_fields = [
                        "show_order_statuses"=>1,
                        "filter_from"=>$cur_month."/01/".$cur_year,
                        "filter_to"=>date("m/d/Y"),
                        "order_statuses"=>"complete",
                        "show_actual_columns"=>1,
                        "name"=>"",
                        "sku"=>"",
                        "period_type"=>"",
                        "customer_email"=>"",
                        "group_by"=>"month",
                        "report_field"=>"main_table.created_at",
                        "lofFilter"=>"",
                        "storeIds"=>""];

        //Convert search criteria to specify filter params
        
        foreach ($searchCriteria->getFilterGroups() as $group) {
            if(!$group)
                continue;
            //var \Magento\Framework\Api\Search\FilterGroup $group
            foreach ($group->getFilters() as $filter) {
                $field = $filter->getField();
                $value = $filter->getValue();
                if($field != "filter_groups" && $field != "sort_orders" && isset($filter_fields[$field])) {
                    $filter_fields[$field] = $value;
                }
            }
        }

        //Init filter data to convert into a filter object of the report
        $this->initFilterData($filter_fields);

        $filterData = $this->getFilterData();
        $store_ids = $this->_getStoreIds();

        $store = $this->_getStore();
        $resourceCollection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
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
            $resourceCollection->joinField(
                'qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
        }
        if ($store->getId()) { 
            $resourceCollection->addStoreFilter($store);
            $resourceCollection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                Store::DEFAULT_STORE_ID
            );
            $resourceCollection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $resourceCollection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $resourceCollection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $resourceCollection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $resourceCollection->addAttributeToSelect('price');
            $resourceCollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $resourceCollection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
    
        //Purchased Products Collection
        $resourceProductCollection = $this->_objectManager->create('Lof\AdvancedReports\Model\ResourceModel\Products\Order\Collection')
            ->prepareListProductCollection()
            ->setDateColumnFilter('created_at')
            ->addDateFromFilter($filterData->getData('filter_from', null))
            ->addDateToFilter($filterData->getData('filter_to', null))
            ->addStoreFilter($store_ids);

        $resourceProductCollection->addOrderStatusFilter($filterData->getData('order_statuses'));

        $resourceProductCollection->getSelect()
                            ->group('product_id');

        $resourceProductCollection->applyCustomFilter();
        $purchased_product_select = $resourceProductCollection->getSelect();
        //Purchased Products Collection 
        if ($purchased_product_select) {
            $resourceCollection->getSelect()->where("e.entity_id NOT IN (?)", $purchased_product_select);
        }

        if($currentPage = $searchCriteria->getCurrentPage()) {
            $resourceCollection->setCurPage($currentPage);
        }
        if($pageSize = $searchCriteria->getPageSize()) {
            $resourceCollection->setPageSize($pageSize);
        }

        $resourceCollection->load();
        $this->_convertGridData($resourceCollection);
        //init \Lof\AdvancedReports\Api\Data\CustomerreportdataInterface
        $searchResult = $this->searchResultsFactory->create();

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($resourceCollection->getItems());
        $searchResult->setTotalCount($resourceCollection->getSize());
        return $searchResult;
    }

    protected function _getPeriodType () {
        return "main_table.name";
    } 

    protected function _getStore()
    {
        $store_ids = $this->_getStoreIds();
        
        if(is_array($store_ids) && isset($store_ids[0]) && $store_ids[0]) {
            $storeId = (int)$store_ids[0];
        } else {
            $storeId = $store_ids?(int)$store_ids:0;
        }
        return $this->_storeManager->getStore($storeId);
    }

    protected function _convertGridData(&$collection = null) {

        if($collection && $collection->getSize()) {
            $filterData = $this->getFilterData();
            $store = $this->_getStore();
            $types = $this->_objectManager->create('\Magento\Catalog\Model\Product\Type')->getOptionArray();

            $attribute_sets = $this->_setsFactory->create()->setEntityTypeFilter(
                    $this->_productFactory->create()->getResource()->getTypeId()
                )->load()->toOptionHash();

            $visibilities = $this->_objectManager->create('\Magento\Catalog\Model\Product\Visibility')->getOptionArray();

            $statuses = $this->_objectManager->create('\Magento\Catalog\Model\Product\Attribute\Source\Status')->getOptionArray();

            $websites_array = $this->_websiteFactory->create()->getCollection()->toOptionHash();

            foreach($collection as &$item) {
                if ($store->getId()) {
                    $custom_name = $store->getName();
                    $item->setData("custom_name", $custom_name);
                }

                $type_id = $item->getTypeId();
                if($types && isset($types[$type_id])) {
                    $item->setData("type_label", $types[$type_id]);
                }

                $attribute_set_id = $item->getAttributeSetId();
                if($attribute_sets && isset($attribute_sets[$attribute_set_id])) {
                    $item->setData("attribute_set_label", $attribute_sets[$attribute_set_id]);
                }


                $price_currency = $this->formatCurrency($item->getPrice());
                $item->setData("price_currency", $price_currency);

                $visibility = $item->getVisibility();
                if($visibilities && isset($visibilities[$visibility])) {
                    $item->setData("visibility_label", $visibilities[$visibility]);
                }

                $status = $item->getStatus();
                if($statuses && isset($statuses[$status])) {
                    $item->setData("status_label", $statuses[$status]);
                }

                $websites = $item->getWebsites();
                if($websites_array && isset($websites_array[$websites])) {
                    $item->setData("websites_label", $websites_array[$websites]);
                }
            }
        }
        return $collection;
    }
}