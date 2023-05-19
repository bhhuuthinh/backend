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

namespace Lof\AdvancedReports\Model\ResourceModel\AbstractReport;

use Lof\AdvancedReports\Model\ResourceModel\Sales\Collection;
use Magento\Framework\DB\Select;

/**
 * Class Ordercollection
 *
 * @package Lof\AdvancedReports\Model\ResourceModel\AbstractReport
 */
class Ordercollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
    protected $_main_table_id = 'main_table.entity_id';
    protected $_status_field = 'main_table.status';
    /**
     * Store ids
     *
     * @var int|array
     */
    protected $_storesIds = 0;
    protected $_period_type = "";
    /**
     * Is live
     *
     * @var boolean
     */
    protected $_isLive = false;
    /**
     * Aggregated columns
     *
     * @var array
     */
    protected $_aggregatedColumns = [];

    /**
     * Sales amount expression
     *
     * @var string
     */
    protected $_salesAmountExpression;

    /**
     * Is totals
     *
     * @var bool
     */
    protected $_isTotals = false;

    /**
     * Is subtotals
     *
     * @var bool
     */
    protected $_isSubTotals = false;

    protected $_year_filter = '';
    protected $_month_filter = '';
    protected $_day_filter = '';
    protected $_orderStatus = "";
    protected $_to_date_filter = "";
    protected $_from_date_filter = "";

    protected $_objectManager;
    protected $_scopeConfig;
    protected $_registry;
    protected $_storeManager;
    protected $_localeDate;
    protected $customerResource;
    protected $_order_rate = '';

    /**
     * Model initialization
     *
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
        $this->_objectManager   = $objectManager;
        $this->_scopeConfig     = $scopeConfig;
        $this->_registry        = $registry;
        $this->_localeDate      = $localeDate;
        $this->_storeManager    = $storeManager;
        $this->customerResource = $customerResource;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Order', '\Magento\Sales\Model\ResourceModel\Order\Collection');
    }

    /**
     * @param string $orderRate
     * @return $this
     */
    public function setOrderRate($orderRate = "")
    {
        $this->_order_rate = $orderRate;

        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setMainTableId($id = "")
    {
        if ($id) {
            $this->_main_table_id = $id;
        }

        return $this;
    }

    /**
     * @param null $flag
     * @return $this|bool
     */
    public function isTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;

        return $this;
    }

    /**
     * @param null $flag
     * @return $this|bool
     */
    public function isSubTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isSubTotals;
        }
        $this->_isSubTotals = $flag;

        return $this;
    }

    /**
     * @param $orderStatus
     * @return $this
     */
    public function addOrderStatusFilter($orderStatus)
    {
        $this->_orderStatus = $orderStatus;

        return $this;
    }

    /**
     * @return $this
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus) || ! $this->_orderStatus) {
            return $this;
        }

        $orderStatus = $this->_orderStatus;
        if (is_array($orderStatus)) {
            if (count($orderStatus) == 1 && strpos($orderStatus[0], ',') !== false) {
                $orderStatus = explode(",", $orderStatus[0]);
            }
        }

        if ( ! is_array($orderStatus)) {
            $orderStatus = explode(",", $orderStatus);
        }

        $this->getSelect()->where($this->_status_field . ' IN(?)', $orderStatus);

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setAggregatedColumns(array $columns)
    {
        $this->_aggregatedColumns = $columns;

        return $this;
    }

    /**
     * Retrieve array of columns that should be aggregated
     *
     * @return mixed
     */
    public function getAggregatedColumns()
    {
        return $this->_aggregatedColumns;
    }

    public function parepareReportCollection()
    {
        return $this;
    }

    /**
     * Retrieve range expression adapted for attribute
     *
     * @param string $range
     * @param string $attribute
     * @return string
     */
    protected function _getRangeExpressionForAttribute($range, $attribute)
    {
        $expression = $this->_getRangeExpression($range);

        return str_replace('{{attribute}}', $this->getConnection()->quoteIdentifier($attribute), $expression);
    }

    /**
     * Retrieve query for attribute with timezone conversion
     *
     * @param string $range
     * @param string $attribute
     * @param mixed  $from
     * @param mixed  $to
     * @return string
     */
    protected function _getTZRangeOffsetExpression($range, $attribute, $from = null, $to = null)
    {
        return str_replace(
            '{{attribute}}',
            $this->_reportOrderFactory->create()->getStoreTZOffsetQuery($this->getMainTable(), $attribute, $from, $to),
            $this->_getRangeExpression($range)
        );
    }

    /**
     * Retrieve range expression with timezone conversion adapted for attribute
     *
     * @param string $range
     * @param string $attribute
     * @param string $tzFrom
     * @param string $tzTo
     * @return string
     */
    protected function _getTZRangeExpressionForAttribute($range, $attribute, $tzFrom = '+00:00', $tzTo = null)
    {
        if (null == $tzTo) {
            $tzTo = $this->_localeDate->scopeDate()->format('P');
        }
        $connection = $this->getConnection();
        $expression = $this->_getRangeExpression($range);
        $attribute  = $connection->quoteIdentifier($attribute);
        $periodExpr = $connection->getDateAddSql(
            $attribute,
            $tzTo,
            \Magento\Framework\DB\Adapter\AdapterInterface::INTERVAL_HOUR
        );

        return str_replace('{{attribute}}', $periodExpr, $expression);
    }


    /**
     * Add item count expression
     *
     * @return $this
     */
    public function addItemCountExpr()
    {
        $this->getSelect()->columns(['items_count' => 'total_item_count'], 'main_table');

        return $this;
    }

    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $adapter              = $this->getConnection();
        $baseSubtotalInvoiced = $adapter->getIfNullSql('main_table.base_subtotal_invoiced', 0);
        $baseDiscountRefunded = $adapter->getIfNullSql('main_table.base_discount_refunded', 0);
        $baseSubtotalRefunded = $adapter->getIfNullSql('main_table.base_subtotal_refunded', 0);
        $baseDiscountInvoiced = $adapter->getIfNullSql('main_table.base_discount_invoiced', 0);
        $baseTotalInvocedCost = $adapter->getIfNullSql('main_table.base_total_invoiced_cost', 0);
        if ($storeIds) {
            $this->getSelect()->columns([
                'subtotal' => 'SUM(main_table.base_subtotal)',
                'tax'      => 'SUM(main_table.base_tax_amount)',
                'shipping' => 'SUM(main_table.base_shipping_amount)',
                'discount' => 'SUM(main_table.base_discount_amount)',
                'total'    => 'SUM(main_table.base_grand_total)',
                'invoiced' => 'SUM(main_table.base_total_paid)',
                'refunded' => 'SUM(main_table.base_total_refunded)',
                'profit'   => "SUM($baseSubtotalInvoiced) "
                              . "+ SUM({$baseDiscountRefunded}) - SUM({$baseSubtotalRefunded}) "
                              . "- SUM({$baseDiscountInvoiced}) - SUM({$baseTotalInvocedCost})",
            ]);
        } else {
            $this->getSelect()->columns([
                'subtotal' => 'SUM(main_table.base_subtotal * main_table.base_to_global_rate)',
                'tax'      => 'SUM(main_table.base_tax_amount * main_table.base_to_global_rate)',
                'shipping' => 'SUM(main_table.base_shipping_amount * main_table.base_to_global_rate)',
                'discount' => 'SUM(main_table.base_discount_amount * main_table.base_to_global_rate)',
                'total'    => 'SUM(main_table.base_grand_total * main_table.base_to_global_rate)',
                'invoiced' => 'SUM(main_table.base_total_paid * main_table.base_to_global_rate)',
                'refunded' => 'SUM(main_table.base_total_refunded * main_table.base_to_global_rate)',
                'profit'   => "SUM({$baseSubtotalInvoiced} *  main_table.base_to_global_rate) "
                              . "+ SUM({$baseDiscountRefunded} * main_table.base_to_global_rate) "
                              . "- SUM({$baseSubtotalRefunded} * main_table.base_to_global_rate) "
                              . "- SUM({$baseDiscountInvoiced} * main_table.base_to_global_rate) "
                              . "- SUM({$baseTotalInvocedCost} * main_table.base_to_global_rate)",
            ]);
        }

        return $this;
    }

    /**
     * Add group By customer attribute
     *
     * @return $this
     */
    public function groupByCustomer()
    {
        $this->getSelect()
             ->where('main_table.customer_id IS NOT NULL')
             ->group('main_table.customer_id');

        /*
         * Allow Analytic functions usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Join Customer Name (concat)
     *
     * @param string $alias
     * @return $this
     */
    public function joinCustomerName($alias = 'name')
    {
        $fields      = [
            'main_table.customer_firstname',
            'main_table.customer_middlename',
            'main_table.customer_lastname',
        ];
        $fieldConcat = $this->getConnection()->getConcatSql($fields, ' ');
        $this->getSelect()->columns([$alias => $fieldConcat]);

        return $this;
    }

    /**
     * Add Order count field to select
     *
     * @return $this
     */
    public function addOrdersCount()
    {
        $this->addFieldToFilter('state', ['neq' => \Magento\Sales\Model\Order::STATE_CANCELED]);
        $this->getSelect()
             ->columns(['orders_count' => 'COUNT(main_table.entity_id)']);

        return $this;
    }

    /**
     * Add revenue
     *
     * @param boolean $convertCurrency
     * @return $this
     */
    public function addRevenueToSelect($convertCurrency = false)
    {
        if ($convertCurrency) {
            $this->getSelect()->columns([
                'revenue' => '(main_table.base_grand_total)',
            ]);
        } else {
            $this->getSelect()->columns([
                'revenue' => 'base_grand_total',
            ]);
        }

        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function orderByTotalAmount($dir = self::SORT_ORDER_DESC)
    {
        $this->getSelect()->order('orders_sum_amount ' . $dir);

        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function orderByOrdersCount($dir = self::SORT_ORDER_DESC)
    {
        $this->getSelect()->order('orders_count ' . $dir);

        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function orderByCustomerRegistration($dir = self::SORT_ORDER_DESC)
    {
        $this->setOrder('customer_id', $dir);

        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function orderByCreatedAt($dir = self::SORT_ORDER_DESC)
    {
        $this->setOrder('created_at', $dir);

        return $this;
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        $countSelect->reset(\Magento\Framework\DB\Select::HAVING);
        $countSelect->columns("COUNT(DISTINCT " . $this->_main_table_id . ")");

        return $countSelect;
    }

    /**
     * @return $this|\Lof\AdvancedReports\Model\ResourceModel\AbstractReport\Ordercollection
     */
    protected function _initInitialFieldsToSelect()
    {
        // No fields should be initialized
        return $this;
    }

    /**
     * @param $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;

        return $this;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @return $this
     */
    protected function _applyStoresFilterToSelect(\Magento\Framework\DB\Select $select)
    {
        $nullCheck = false;
        $storeIds  = $this->_storesIds;

        if ($storeIds) {
            if ( ! is_array($storeIds)) {
                $storeIds = [$storeIds];
            }

            $storeIds = array_unique($storeIds);

            if ($index = array_search(null, $storeIds)) {
                unset($storeIds[$index]);
                $nullCheck = true;
            }

            $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

            if ($nullCheck) {
                $select->where('main_table.store_id IN(?) OR main_table.store_id IS NULL', $storeIds);
            } else {
                $select->where('main_table.store_id IN(?)', $storeIds);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _applyStoresFilter()
    {
        return $this->_applyStoresFilterToSelect($this->getSelect());
    }

    public function applyCustomFilter()
    {
        return $this;
    }

    public function getArrayItems($column_name = "")
    {
        $items = [];
        foreach ($this as $i => $item) {
            $key_value = $item->getData($column_name);
            if ($key_value !== null && $key_value !== false) {
                $items[$key_value] = $item;
            }
        }

        return $items;
    }


    /**
     * Verifies the transition and the "to" timestamp
     *
     * @param array      $transition
     * @param int|string $to
     * @return bool
     */
    protected function _isValidTransition($transition, $to)
    {
        $result         = true;
        $timeStamp      = $transition['ts'];
        $transitionYear = date('Y', $timeStamp);

        if ($transitionYear > 10000 || $transitionYear < -10000) {
            $result = false;
        } elseif ($timeStamp > $to) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $from
     * @param $to
     * @return $this
     */
    protected function _checkDates(&$from, &$to)
    {
        if ($from !== null) {
            $from = $this->formatDate($from);
        }

        if ($to !== null) {
            $to = $this->formatDate($to);
        }

        return $this;
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->setOrigData();

            if ($item instanceof \Magento\Framework\Model\AbstractModel) {
                if ($this->_resetItemsDataChanged) {
                    $item->setDataChanges(false);
                }
            }
        }
        $this->_eventManager->dispatch('core_collection_abstract_load_after', ['collection' => $this]);
        if ($this->_eventPrefix && $this->_eventObject) {
            $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', [$this->_eventObject => $this]);
        }

        return $this;
    }

    /**
     * @param        $date
     * @param string $format
     * @return string
     * @throws \Exception
     */
    public function convertConfigTimeToUtc($date, $format = 'Y-m-d H:i:s')
    {
        if ( ! ($date instanceof \DateTime)) {
            $date = new \DateTime($date, new \DateTimeZone($this->_localeDate->getConfigTimezone()));
        } else {
            if ($date->getTimezone()->getName() !== $this->_localeDate->getConfigTimezone()) {
                throw new LocalizedException(
                    new Phrase('DateTime object timezone must be the same as config - %1', $this->_localeDate->getConfigTimezone())
                );
            }
        }

        // $date->setTimezone(new \DateTimeZone('UTC'));

        return $date->format($format);
    }
}
