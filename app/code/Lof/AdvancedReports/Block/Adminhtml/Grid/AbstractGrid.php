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

namespace Lof\AdvancedReports\Block\Adminhtml\Grid;

class AbstractGrid extends \Magento\Backend\Block\Widget\Grid\Extended {
    protected $_resourceCollectionName = '';
    protected $_currentCurrencyCode = null;
    protected $_storeIds = [];
    protected $_aggregatedColumns = null;
    protected $_is_column_grouped = false;
    protected $_columnDate = 'main_table.created_at';
    protected $_columnGroupBy = '';
    protected $_defaultSort = 'period';
    protected $_defaultDir = 'ASC';
    protected $_resource_grid_collection = null;
    protected $_reportsData = null;
    protected $_collectionFactory;
    protected $_resourceFactory;
    protected $_registry = null;
    protected $_objectManager;
    protected $_scopeConfig;
    protected $_systemStore;
    protected $_setsFactory;
    protected $_websiteFactory;
    protected $moduleManager;
    protected $_productFactory;
    protected $_productConfigurable;

    /**
     * Payment method factory
     *
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $_paymentMethodFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context                        $context
     * @param \Magento\Backend\Helper\Data                                   $backendHelper
     * @param \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory
     * @param \Magento\Reports\Model\Grouped\CollectionFactory               $collectionFactory
     * @param \Magento\Reports\Helper\Data                                   $reportsData
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory,
        \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory,
        \Magento\Reports\Helper\Data $reportsData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        // \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productConfigurable,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        array $data = []
    )
    {
        $this->_objectManager       = $objectManager;
        $this->_registry            = $registry;
        $this->_resourceFactory     = $resourceFactory;
        $this->_collectionFactory   = $collectionFactory;
        $this->_scopeConfig         = $context->getScopeConfig();
        $this->_reportsData         = $reportsData;
        $this->_systemStore         = $systemStore;
        $this->_setsFactory         = $setsFactory;
        $this->_websiteFactory      = $websiteFactory;
        $this->moduleManager        = $moduleManager;
        $this->_productFactory      = $productFactory;
        $this->_productConfigurable = $productConfigurable;
        $this->_paymentMethodFactory = $paymentMethodFactory;
        parent::__construct( $context, $backendHelper, $data );
    }

    /**
     * Pseudo constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setFilterVisibility( false );
        $this->setPagerVisibility( false );
        $this->setUseAjax( false );
        if ( isset( $this->_columnGroupBy ) ) {
            $this->isColumnGrouped( $this->_columnGroupBy, true );
        }
        $this->setEmptyCellLabel( __( 'We can\'t find records for this period.' ) );
    }


    public function setCulumnDate( $_columnDate = "" )
    {
        if ( $_columnDate ) {
            $this->_columnDate = $_columnDate;
        }
    }

    public function setDefaultSort( $_columnSort = "" )
    {
        if ( $_columnSort ) {
            $this->_defaultSort = $_columnSort;
        }
    }

    public function setDefaultDir( $_dir = "" )
    {
        if ( $_dir ) {
            $this->_defaultDir = $_dir;
        }
    }

    public function getColumnOrder()
    {
        return $columnId = $this->getParam( $this->getVarNameSort(), $this->_defaultSort );
    }

    public function getColumnDir()
    {
        return $columnId = $this->getParam( $this->getVarNameDir(), $this->_defaultDir );
    }

    //Add following function
    protected function _prepareTotals( $columns = null )
    {
        if ( $this->hasData( "is_exporting" ) && $this->getIsExporting() ) {
            return;
        }
        $columns = explode( ',', $columns );
        if ( ! $columns ) {
            return;
        }
        $this->_countTotals = true;
        $totals             = new \Magento\Framework\DataObject();
        $fields             = [];
        foreach ( $columns as $column ) {
            $fields[ $column ] = 0;
        }
        foreach ( $this->getCollection() as $item ) {
            foreach ( $fields as $field => $value ) {
                $fields[ $field ] += $item->getData( $field );
            }
        }
        $totals->setData( $fields );
        $this->setTotals( $totals );

        return;
    }

    public function getResourceCollectionName()
    {
        return $this->_resourceCollectionName;
    }

    /**
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        if ( $this->_collection === null ) {
            $this->setCollection( $this->_collectionFactory->create() );
        }

        return $this->_collection;
    }

    protected function _getAggregatedColumns()
    {
        if ( is_null( $this->_aggregatedColumns ) ) {
            foreach ( $this->getColumns() as $column ) {
                if ( ! is_array( $this->_aggregatedColumns ) ) {
                    $this->_aggregatedColumns = [];
                }
                if ( $column->hasTotal() ) {
                    $this->_aggregatedColumns[ $column->getId() ] = "{$column->getTotal()}({$column->getIndex()})";
                }
            }
        }

        return $this->_aggregatedColumns;
    }

    /**
     * Add column to grid
     * Overriden to add support for visibility_filter column option
     * It stands for conditional visibility of the column depending on filter field values
     * Value of visibility_filter supports (filter_field_name => filter_field_value) pairs
     *
     * @param string $columnId
     * @param array  $column
     * @return  Mage_Adminhtml_Block_Report_Grid_Abstract
     */
    public function addColumn( $columnId, $column )
    {
        if ( is_array( $column ) && array_key_exists( 'visibility_filter', $column ) ) {
            $filterData       = $this->getFilterData();
            $visibilityFilter = $column['visibility_filter'];
            if ( ! is_array( $visibilityFilter ) ) {
                $visibilityFilter = [ $visibilityFilter ];
            }
            foreach ( $visibilityFilter as $k => $v ) {
                if ( is_int( $k ) ) {
                    $filterFieldId    = $v;
                    $filterFieldValue = true;
                } else {
                    $filterFieldId    = $k;
                    $filterFieldValue = $v;
                }
                if (
                    ! $filterData->hasData( $filterFieldId ) ||
                    $filterData->getData( $filterFieldId ) != $filterFieldValue
                ) {
                    return $this;  // don't add column
                }
            }
        }

        return parent::addColumn( $columnId, $column );
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return  array
     */
    protected function _getStoreIds()
    {
        $filterData = $this->getFilterData();
        if ( $filterData ) {
            $storeIds = explode( ',', $filterData->getData( 'store_ids' ) );
        } else {
            $storeIds = [];
        }
        // By default storeIds array contains only allowed stores
        $allowedStoreIds = array_keys( $this->_storeManager->getStores() );
        // And then array_intersect with post data for prevent unauthorized stores reports
        $storeIds = array_intersect( $allowedStoreIds, $storeIds );
        // If selected all websites or unauthorized stores use only allowed
        if ( empty( $storeIds ) ) {
            $storeIds = $allowedStoreIds;
        }
        // reset array keys
        $storeIds = array_values( $storeIds );

        return $storeIds;
    }

    public function setStoreIds( $storeIds )
    {
        $this->_storeIds = $storeIds;

        return $this;
    }

    /**
     * @param null $currency_code
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentCurrencyCode( $currency_code = null )
    {
        if ( $currency_code ) {
            return $currency_code;
        }

        if ( $this->_currentCurrencyCode === null ) {
            $storeIds = $this->_storeIds;
            if ( ! $storeIds ) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
            }

            if ( count($storeIds) == 1 ) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore($storeIds[0])->getBaseCurrencyCode();;
            }

            if ( count($storeIds) > 1 ) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
            }
        }

        return $this->_currentCurrencyCode;
    }

    /**
     * @param $toCurrency
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRate( $toCurrency )
    {
        $storeIds = $this->_getStoreIds();

        if ( ! $storeIds ) {
            return $this->_storeManager->getStore()->getBaseCurrency()->getRate( $toCurrency );
        }

        if ( count($storeIds) == 1 ) {
            return $this->_storeManager->getStore($storeIds[0])->getBaseCurrency()->getRate( $toCurrency );
        }

        if ( count($storeIds) > 1 ) {
            return $this->_storeManager->getStore()->getBaseCurrency()->getRate( $toCurrency );
        }
    }

    /**
     * @param $collection
     * @param $filterData
     * @return $this
     */
    protected function _addOrderStatusFilter( $collection, $filterData )
    {
        if ( $filterData->getData( 'order_statuses' ) ) {
            $collection->addOrderStatusFilter( $filterData->getData( 'order_statuses' ) );
        }

        return $this;
    }

    /**
     * Adds custom filter to resource collection
     * Can be overridden in child classes if custom filter needed
     *
     * @param Mage_Reports_Model_Resource_Report_Collection_Abstract $collection
     * @param Varien_Object                                          $filterData
     * @return Mage_Adminhtml_Block_Report_Grid_Abstract
     */
    protected function _addCustomFilter( $collection, $filterData )
    {
        $grid_filter = $this->getParam( $this->getVarNameFilter(), null );

        if ( is_null( $grid_filter ) ) {
            $grid_filter = $this->_defaultFilter;
        }
        $custom_filters = [];
        if ( is_string( $grid_filter ) ) {
            $data        = [];
            $grid_filter = base64_decode( $grid_filter );
            parse_str( urldecode( $grid_filter ), $data );
            $custom_filters = $data;
        } elseif ( $grid_filter && is_array( $grid_filter ) ) {
            $custom_filters = $grid_filter;
        }

        if ( $custom_filters ) {
            foreach ( $custom_filters as $name => $value ) {
                if ( $custom_filters[ $name ] && is_string( $custom_filters[ $name ] ) ) {
                    $collection->addFieldToFilter( $name, [ "like" => '%' . $custom_filters[ $name ] . '%' ] );
                } elseif ( $custom_filters[ $name ] && is_array( $custom_filters[ $name ] ) ) {
                    $from_value = isset( $custom_filters[ $name ][0] ) ? $custom_filters[ $name ][0] : false;
                    $from_value = ( $from_value == false && isset( $custom_filters[ $name ]['from'] ) ) ? $custom_filters[ $name ]['from'] : '';
                    $to_value   = isset( $custom_filters[ $name ][1] ) ? $custom_filters[ $name ][1] : false;
                    $to_value   = ( $to_value == false && isset( $custom_filters[ $name ]['to'] ) ) ? $custom_filters[ $name ]['to'] : '';

                    if ( $from_value != '' ) {
                        $collection->addFieldToFilter( $name, [ "gteq" => $from_value ] );
                    }
                    if ( $to_value != '' ) {
                        $collection->addFieldToFilter( $name, [ "lteq" => $to_value ] );
                    }
                }
            }
        }

        return $this;
    }
}
