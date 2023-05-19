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

namespace Lof\AdvancedReports\Model\ResourceModel\Sales;

/**
 * Class Collection
 *
 * @package Lof\AdvancedReports\Model\ResourceModel\Sales
 */
class  Collection extends \Lof\AdvancedReports\Model\ResourceModel\AbstractReport\Ordercollection {
    protected $_date_column_filter = "main_table.created_at";
    protected $_period_type = "";
    protected $_product_name_filter = "";
    protected $_product_sku_filter = "";
    protected $_product_id_filter = "";
    protected $_order_rate = "";

    /**
     * Is live
     *
     * @var boolean
     */
    protected $_isLive = false;

    /**
     * Sales amount expression
     *
     * @var string
     */
    protected $_salesAmountExpression;

    /**
     * @param string $column_name
     * @return $this
     */
    public function setDateColumnFilter($column_name = '')
    {
        if ($column_name) {
            $this->_date_column_filter = $column_name;
        }

        return $this;
    }

    public function getDateColumnFilter()
    {
        return $this->_date_column_filter;
    }

    /**
     * @param null $from
     * @return $this
     */
    public function addDateFromFilter($from = null)
    {
        $this->_from_date_filter = $from;

        return $this;
    }

    /**
     * @param null $to
     * @return $this
     */
    public function addDateToFilter($to = null)
    {
        $this->_to_date_filter = $to;

        return $this;
    }

    /**
     * @param string $period_type
     * @return $this
     */
    public function setPeriodType($period_type = "")
    {
        $this->_period_type = $period_type;

        return $this;
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
     * @param int $product_id
     * @return $this
     */
    public function addProductIdFilter($product_id = 0)
    {
        $this->_product_id_filter = $product_id;

        return $this;
    }

    /**
     * @param string $product_sku
     * @return $this
     */
    public function addProductSkuFilter($product_sku = "")
    {
        $this->_product_sku_filter = $product_sku;

        return $this;
    }

    /**
     * @param int $category_id
     * @return $this
     */
    public function addCategoryIdFilter($category_id = 0)
    {
        $this->_category_id_filter = $category_id;

        return $this;
    }

    /**
     * @return $this
     */
    protected function _applyDateFilter()
    {
        $select_datefield = [];
        if ($this->_period_type) {
            switch ($this->_period_type) {
                case "year":
                    $select_datefield = [
                        'period' => 'YEAR(' . $this->getDateColumnFilter() . ')',
                    ];
                    break;
                case "quarter":
                    $select_datefield = [
                        'period' => 'CONCAT(QUARTER(' . $this->getDateColumnFilter() . '),"/",YEAR(' . $this->getDateColumnFilter() . '))',
                    ];
                    break;
                case "week":
                    $select_datefield = [
                        'period' => 'CONCAT(YEAR(' . $this->getDateColumnFilter() . '),"", WEEK(' . $this->getDateColumnFilter() . '))',
                    ];
                    break;
                case "day":
                    $select_datefield = [
                        'period' => 'DATE(' . $this->getDateColumnFilter() . ')',
                    ];
                    break;
                case "hour":
                    $select_datefield = [
                        'period' => "DATE_FORMAT(" . $this->getDateColumnFilter() . ", '%H:00')",
                    ];
                    break;
                case "weekday":
                    $select_datefield = [
                        'period' => 'WEEKDAY(' . $this->getDateColumnFilter() . ')',
                    ];
                    break;
                case "month":
                default:
                    $select_datefield = [
                        'period'      => 'CONCAT(MONTH(' . $this->getDateColumnFilter() . '),"/",YEAR(' . $this->getDateColumnFilter() . '))',
                        'period_sort' => 'CONCAT(MONTH(' . $this->getDateColumnFilter() . '),"",YEAR(' . $this->getDateColumnFilter() . '))',
                    ];
                    break;
            }
        }

        if ($select_datefield) {
            $this->getSelect()->columns($select_datefield);
        }

        if ($this->_to_date_filter && $this->_from_date_filter) {
            $dateStart = $this->convertConfigTimeToUtc($this->_from_date_filter, 'Y-m-d 00:00:00');
            $endStart  = $this->convertConfigTimeToUtc($this->_to_date_filter, 'Y-m-d 23:59:59');
            $dateRange = ['from' => $dateStart, 'to' => $endStart, 'datetime' => true];

            $this->addFieldToFilter($this->getDateColumnFilter(), $dateRange);
        }


        return $this;
    }

    public function _applyOrderRateFilter()
    {

    }

    public function prepareCategoryReportCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->setMainTableId("category_id");
        $this->setMainTable('sales_order');
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->_aggregateByField('category_id', $hide_fields);
        $this->join(['category_product' => 'catalog_category_product'], 'category_product.product_id=oi.product_id', ['category_id']);

        return $this;
    }

    public function preparePaymentReportCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->_aggregateByField('payment.method', $hide_fields);
        $this->join(['payment' => 'sales_order_payment'], 'main_table.entity_id=payment.parent_id', 'method');

        return $this;
    }

    public function prepareCustomergroupCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->_aggregateByField('group_name', $hide_fields);
        $this->join(['c' => 'customer_entity'], 'main_table.customer_id=c.entity_id', '');
        $this->join(['cg' => 'customer_group'], 'c.group_id=cg.customer_group_id', ['group_name' => 'customer_group_code'], null, 'left');

        return $this;
    }

    public function prepareStatisticsCollection()
    {
        $this->_aggregateByField('period');

        return $this;
    }

    public function prepareOverviewCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->_aggregateByField('period', $hide_fields);

        return $this;
    }

    public function prepareHourlyCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->_aggregateByField('period', $hide_fields);

        return $this;
    }

    public function prepareWeekdayCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->_aggregateByField('period', $hide_fields);

        return $this;
    }

    public function prepareProducttypeCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->_aggregateByField('product_type', $hide_fields);

        return $this;
    }

    public function prepareByCountryCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->setMainTableId("country_id");
        $this->_aggregateByField('country_id', $hide_fields);
        $adapter       = $this->getResource()->getConnection();
        $selectaddress = $adapter->select();
        $colsaddress   = [
            "customer_address_id" => "customer_address_id",
            "region"              => "region",
            "postcode"            => "postcode",
            "street"              => "street",
            "city"                => "city",
            "country_id"          => "country_id",
            "parent_id"           => "parent_id",
        ];
        $selectaddress->from($this->getTable('sales_order_address'), $colsaddress)
                      ->group('parent_id');
        $this->getSelect()->join(['oadd' => $selectaddress], 'oadd.parent_id = main_table.entity_id', ["region", "postcode", "street", "city", "country_id"]);
        // $this->getSelect()
        //         ->join(array('oadd' => $this->getTable('sales_order_address')), 'main_table.entity_id = oadd.parent_id', array("region","postcode","street","city","country_id"));
        return $this;
    }

    public function prepareByPostcodeCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->setMainTableId("postcode");
        $this->_aggregateByField('postcode', $hide_fields);
        $adapter       = $this->getResource()->getConnection();
        $selectaddress = $adapter->select();
        $colsaddress   = [
            "customer_address_id" => "customer_address_id",
            "region"              => "region",
            "postcode"            => "postcode",
            "street"              => "street",
            "city"                => "city",
            "country_id"          => "country_id",
            "parent_id"           => "parent_id",
        ];
        $selectaddress->from($this->getTable('sales_order_address'), $colsaddress)
                      ->group('parent_id');
        $this->getSelect()->join(['oadd' => $selectaddress], 'oadd.parent_id = main_table.entity_id', ["region", "postcode", "street", "city", "country_id"]);

        return $this;
    }

    public function prepareByRegionCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->setMainTableId("region");
        $this->_aggregateByField('region', $hide_fields);
        $adapter       = $this->getResource()->getConnection();
        $selectaddress = $adapter->select();
        $colsaddress   = [
            "customer_address_id" => "customer_address_id",
            "region"              => "region",
            "postcode"            => "postcode",
            "street"              => "street",
            "city"                => "city",
            "country_id"          => "country_id",
            "parent_id"           => "parent_id",
        ];
        $selectaddress->from($this->getTable('sales_order_address'), $colsaddress)
                      ->group('parent_id');
        $this->getSelect()->join(['oadd' => $selectaddress], 'oadd.parent_id = main_table.entity_id', ["region", "postcode", "street", "city", "country_id"]);

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function prepareByCouponCollection()
    {
        $hide_fields = ["avg_item_cost", "avg_order_amount"];
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->setMainTableId("coupon_code");
        $this->_aggregateByField('coupon_code', $hide_fields);
        $this->getSelect()
             ->where('coupon_code IS NOT NULL');

        return $this;
    }

    public function applyCustomFilter()
    {
        $this->_applyDateFilter();
        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();

        return $this;
    }

    public function applyProductFilter()
    {
        if ($this->_product_id_filter) {
            $this->addFieldToFilter("oi.product_id", $this->_product_id_filter);
        }
        if ($this->_product_sku_filter) {
            $this->addFieldToFilter("oi.sku", $this->_product_sku_filter);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function applyCategoryFilter()
    {
        if ($this->_category_id_filter) {
            $this->addFieldToFilter("category_product.category_id", $this->_category_id_filter);
        }

        return $this;
    }

    /**
     * @param string $aggregationField
     * @param array  $hide_fields
     * @param array  $show_fields
     * @return $this
     */
    public function _aggregateByField($aggregationField = "", $hide_fields = [], $show_fields = [])
    {
        $adapter = $this->getResource()->getConnection();
        try {

            $this->getSelect()->columns(['order_currency' => 'main_table.base_shipping_invoiced']);
            $subSelect = null;
            // Columns list
            $columns = [
                'rate'                         => 'currency_rate.rate',
                'order_ids'                    => 'GROUP_CONCAT(DISTINCT main_table.entity_id SEPARATOR \',\')',
                'coupon_code'                  => 'main_table.coupon_code',
                'store_id'                     => 'main_table.store_id',
                'order_status'                 => 'main_table.status',
                'product_type'                 => 'oi.product_type',
                'orders_count'                 => new \Zend_Db_Expr('COUNT(main_table.entity_id)'),
                'total_qty_ordered'            => new \Zend_Db_Expr('SUM(oi.total_qty_ordered)'),
                'total_subtotal_amount'        => new \Zend_Db_Expr('SUM(main_table.subtotal / currency_rate.rate)'),
                'total_qty_invoiced'           => new \Zend_Db_Expr('SUM(oi.total_qty_invoiced)'),
                'total_grandtotal_amount'      => new \Zend_Db_Expr('SUM(main_table.grand_total / currency_rate.rate)'),
                'avg_item_cost'                => new \Zend_Db_Expr('AVG(oi.total_item_cost / currency_rate.rate)'),
                'avg_order_amount'             => new \Zend_Db_Expr(
                    sprintf('AVG((%s - %s - %s - (%s - %s - %s)) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0)
                    )
                ),
                'total_income_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.grand_total', 0),
                        $adapter->getIfNullSql('main_table.total_canceled', 0)
                    )
                ),
                'total_revenue_amount'         => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s - %s - (%s - %s - %s)) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0)
                    )
                ),
                'total_profit_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(((%s - %s) - (%s - %s) - (%s - %s) ) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_paid', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0)
                    )
                ),
                'total_invoiced_amount'        => new \Zend_Db_Expr(
                    sprintf('SUM(%s / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_invoiced', 0)
                    )
                ),
                'total_canceled_amount'        => new \Zend_Db_Expr(
                    sprintf('SUM(%s / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_canceled', 0)
                    )
                ),
                'total_paid_amount'            => new \Zend_Db_Expr(
                    sprintf('SUM(%s / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_paid', 0)
                    )
                ),
                'total_refunded_amount'        => new \Zend_Db_Expr(
                    sprintf('SUM(%s / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_refunded', 0)
                    )
                ),
                'total_tax_amount'             => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.tax_amount', 0),
                        $adapter->getIfNullSql('main_table.tax_canceled', 0)
                    )
                ),
                'total_tax_amount_actual'      => new \Zend_Db_Expr(
                    sprintf('SUM((%s -%s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0)
                    )
                ),
                'total_shipping_amount'        => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.shipping_amount', 0),
                        $adapter->getIfNullSql('main_table.shipping_canceled', 0)
                    )
                ),
                'total_shipping_amount_actual' => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0)
                    )
                ),
                'total_discount_amount'        => new \Zend_Db_Expr(
                    sprintf('SUM((ABS(%s) - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.discount_amount', 0),
                        $adapter->getIfNullSql('main_table.discount_canceled', 0)
                    )
                ),
                'total_discount_amount_actual' => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.discount_invoiced', 0),
                        $adapter->getIfNullSql('main_table.discount_refunded', 0)
                    )
                ),
            ];

            if ($hide_fields) {
                foreach ($hide_fields as $field) {
                    if (isset($columns[$field])) {
                        unset($columns[$field]);
                    }
                }
            }

            $selectOrderItem = $adapter->select();

            $qtyCanceledExpr = $adapter->getIfNullSql('qty_canceled', 0);
            $cols            = [
                'order_id'           => 'order_id',
                'product_id'         => 'product_id',
                'product_type'       => 'product_type',
                'created_at'         => 'created_at',
                'sku'                => 'sku',
                'total_qty_ordered'  => new \Zend_Db_Expr("SUM(qty_ordered - {$qtyCanceledExpr})"),
                'total_qty_invoiced' => new \Zend_Db_Expr('SUM(qty_invoiced)'),
                'total_item_cost'    => new \Zend_Db_Expr('SUM(row_total)'),
            ];

            $selectOrderItem->from($this->getTable('sales_order_item'), $cols)
                            ->where('parent_item_id IS NULL')
                            ->group('order_id');

            $this->getSelect()->columns($columns)
                 ->join(['oi' => $selectOrderItem], 'oi.order_id = main_table.entity_id', [])
                 ->where('main_table.state NOT IN (?)', [
                     \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT,
                 ]);

            if ($aggregationField) {
                $this->getSelect()->group($aggregationField);
            }

        } catch (\Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        $this->join(['currency_rate' => 'directory_currency_rate'], "main_table.order_currency_code=currency_rate.currency_to AND currency_rate.currency_from='{$this->_order_rate}'",
            ['rate']);

        return $this;
    }
}
