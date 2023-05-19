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

namespace Lof\AdvancedReports\Model\ResourceModel\Order;
class  Collection extends \Lof\AdvancedReports\Model\ResourceModel\AbstractReport\Ordercollection
{
    /**
     * Is live
     *
     * @var boolean
     */
    protected $_isLive   = false;
    protected $_order_rate = '';


    /**
     * Sales amount expression
     *
     * @var string
     */
    protected $_salesAmountExpression;

    /**
     * Check range for live mode
     *
     * @param unknown_type $range
     * @return Mage_Reports_Model_Resource_Order_Collection
     */


    public function setDateColumnFilter($column_name = '') {
        if($column_name) {
            $this->_date_column_filter = $column_name;
        }
        return $this;
    }

    /**
     * @param string $orderRate
     * @return \Lof\AdvancedReports\Model\ResourceModel\Sales\Collection
     */
    public function setOrderRate($orderRate = "")
    {
        $this->_order_rate = $orderRate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateColumnFilter() {
        return $this->_date_column_filter;
    }
    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function addDateFromFilter($from = null)
    {
        $this->_from_date_filter = $from;
        return $this;
    }

    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function addDateToFilter($to = null)
    {
        $this->_to_date_filter = $to;
        return $this;
    }

    public function setPeriodType($period_type = "") {
        $this->_period_type = $period_type;
        return $this;
    }

    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function addProductIdFilter($product_id = 0)
    {
        $this->_product_id_filter = $product_id;
        return $this;
    }

    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function addProductSkuFilter($product_sku = "")
    {
        $this->_product_sku_filter = $product_sku;
        return $this;
    }


    protected function _applyDateFilter()
    {
        $select_datefield = array();
        if($this->_period_type) {
            switch( $this->_period_type) {
                case "year":
                    $select_datefield = array(
                        'period'  => 'YEAR('.$this->getDateColumnFilter().')'
                    );
                    break;
                case "quarter":
                    $select_datefield = array(
                        'period'  => 'CONCAT(QUARTER('.$this->getDateColumnFilter().'),"/",YEAR('.$this->getDateColumnFilter().'))'
                    );
                    break;
                case "week":
                    $select_datefield = array(
                        'period'  => 'CONCAT(YEAR('.$this->getDateColumnFilter().'),"", WEEK('.$this->getDateColumnFilter().'))'
                    );
                    break;
                case "day":
                    $select_datefield = array(
                        'period'  => 'DATE('.$this->getDateColumnFilter().')'
                    );
                    break;
                case "hour":
                    $select_datefield = array(
                        'period'  => "DATE_FORMAT(".$this->getDateColumnFilter().", '%H:00')"
                    );
                    break;
                case "weekday":
                    $select_datefield = array(
                        'period'  => 'WEEKDAY('.$this->getDateColumnFilter().')'
                    );
                    break;
                case "month":
                default:
                    $select_datefield = array(
                        'period'  => 'CONCAT(MONTH('.$this->getDateColumnFilter().'),"/",YEAR('.$this->getDateColumnFilter().'))',
                        'period_sort'  => 'CONCAT(MONTH('.$this->getDateColumnFilter().'),"",YEAR('.$this->getDateColumnFilter().'))'
                    );
                    break;
            }
        }
        if($select_datefield) {
            $this->getSelect()->columns($select_datefield);
        }


        // sql theo filter date
        if($this->_to_date_filter && $this->_from_date_filter) {

            // kiem tra lai doan convert ngay thang nay !

            $dateStart = $this->_localeDate->convertConfigTimeToUtc($this->_from_date_filter,'Y-m-d 00:00:00');
            $endStart = $this->_localeDate->convertConfigTimeToUtc($this->_to_date_filter, 'Y-m-d 23:59:59');
            $dateRange = array('from' => $dateStart, 'to' => $endStart , 'datetime' => true);

            $this->addFieldToFilter($this->getDateColumnFilter(), $dateRange);
        }


        return $this;
    }

    public function applyCustomFilter() {
        $this->_applyDateFilter();
        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();
        return $this;
    }

    public function prepareOrderDetailedCollection() {
        $hide_fields = array("avg_item_cost", "avg_order_amount");
        $this->setMainTableId('main_table.entity_id');
        $this->_aggregateByField('main_table.entity_id', $hide_fields);
        return $this;
    }

    public function prepareOrderItemDetailedCollection() {
        $hide_fields = array("avg_item_cost", "avg_order_amount");
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->_aggregateOrderItemsByField('', $hide_fields);
        return $this;
    }


    /**
     * @param string $aggregationField
     * @param array  $hide_fields
     * @param array  $show_fields
     * @return $this
     */
    protected function _aggregateByField($aggregationField = "", $hide_fields = array(), $show_fields = array())
    {
        $adapter = $this->getResource()->getConnection();
        // $adapter->beginTransaction();
        try {

            $subSelect = null;
            // Columns list
            $columns = array(
                'rate'                         => 'currency_rate.rate',
                'order_ids'                    => 'GROUP_CONCAT(DISTINCT main_table.entity_id SEPARATOR \',\')',
                'customer_firstname'             => new \Zend_Db_Expr('IFNULL(main_table.customer_firstname, "Guest")'),
                'customer_lastname'              => new \Zend_Db_Expr('IFNULL(main_table.customer_lastname, "Guest")'),
                'store_id'                       => 'main_table.store_id',
                'order_status'                   => 'main_table.status',
                'product_type'                   => 'oi.product_type',
                'total_cost_amount'              => new \Zend_Db_Expr('IFNULL(SUM(oi.total_cost_amount / currency_rate.rate),0)'),
                'orders_count'                   => new \Zend_Db_Expr('COUNT(main_table.entity_id)'),
                'total_qty_ordered'              => new \Zend_Db_Expr('SUM(oi.total_qty_ordered)'),
                'total_qty_shipping'             => new \Zend_Db_Expr('SUM(oi.total_qty_shipping)'),
                'total_qty_refunded'             => new \Zend_Db_Expr('SUM(oi.total_qty_refunded)'),
                'total_subtotal_amount'          => new \Zend_Db_Expr('SUM(main_table.subtotal / currency_rate.rate)'),
                'total_qty_invoiced'             => new \Zend_Db_Expr('SUM(oi.total_qty_invoiced)'),
                'total_grandtotal_amount'        => new \Zend_Db_Expr('SUM(main_table.grand_total)'),
                'avg_item_cost'                  => new \Zend_Db_Expr('AVG(oi.total_item_cost)'),
                'avg_order_amount'               => new \Zend_Db_Expr(
                    sprintf('AVG((%s - %s - %s - (%s - %s - %s)) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0)
                    )
                ),
                'total_income_amount'            => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.grand_total', 0),
                        $adapter->getIfNullSql('main_table.total_canceled',0)
                    )
                ),
                'total_revenue_amount'           => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s - %s - (%s - %s - %s)) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0)
                    )
                ),
                'total_profit_amount'            => new \Zend_Db_Expr(
                    sprintf('SUM(((%s - %s) - (%s - %s) - (%s - %s) - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_paid', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_total_invoiced_cost', 0)
                    )
                ),
                'total_invoiced_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_invoiced', 0)
                    )
                ),
                'total_canceled_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_canceled', 0)
                    )
                ),
                'total_paid_amount'              => new \Zend_Db_Expr(
                    sprintf('SUM(%s / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_paid', 0)
                    )
                ),
                'total_refunded_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_refunded', 0)
                    )
                ),
                'total_tax_amount'               => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.tax_amount', 0),
                        $adapter->getIfNullSql('main_table.tax_canceled', 0)
                    )
                ),
                'total_tax_amount_actual'        => new \Zend_Db_Expr(
                    sprintf('SUM((%s -%s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0)
                    )
                ),
                'total_shipping_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.shipping_amount', 0),
                        $adapter->getIfNullSql('main_table.shipping_canceled', 0)
                    )
                ),
                'total_shipping_amount_actual'   => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0)
                    )
                ),
                'total_discount_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM((ABS(%s) - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.discount_amount', 0),
                        $adapter->getIfNullSql('main_table.discount_canceled', 0)
                    )
                ),
                'total_discount_amount_actual'   => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.discount_invoiced', 0),
                        $adapter->getIfNullSql('main_table.discount_refunded', 0)
                    )
                ),
                'total_grossprofit_amount'            => new \Zend_Db_Expr(
                    sprintf('SUM(((%s - %s) - (%s - %s) - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_paid', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_total_invoiced_cost', 0)
                    )
                ),
                'total_net_profits'   => new \Zend_Db_Expr(
                    sprintf('SUM(((%s - %s) - (%s - %s) - (%s - %s) - %s) / currency_rate.rate)',
                        $adapter->getIfNullSql('main_table.total_paid', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_total_invoiced_cost', 0)
                    )
                ),
                'margin_profit'   => new \Zend_Db_Expr(
                    sprintf('ROUND((((SUM(((%s - %s) - (%s - %s) - (%s - %s) - %s)))/ (SUM((%s - %s) )))*100 / currency_rate.rate), 2)',
                        $adapter->getIfNullSql('main_table.total_paid', 0),
                        $adapter->getIfNullSql('main_table.total_refunded', 0),
                        $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.shipping_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_total_invoiced_cost', 0),
                        $adapter->getIfNullSql('main_table.grand_total', 0),
                        $adapter->getIfNullSql('main_table.total_canceled',0)
                    )
                )
            );

            if($hide_fields) {
                foreach($hide_fields as $field){
                    if(isset($columns[$field])){
                        unset($columns[$field]);
                    }
                }
            }

            $selectOrderItem = $adapter->select();

            $cols1 = array(
                'order_id'           => 'order_id',
                'total_parent_cost_amount'  => new \Zend_Db_Expr($adapter->getIfNullSql('SUM(base_cost)',0)),
            );
            $selectOrderItem1 = $adapter->select()->from($this->getTable('sales_order_item'), $cols1)->where('parent_item_id IS NOT NULL')->group('order_id');
            $qtyCanceledExpr = $adapter->getIfNullSql('qty_canceled', 0);
            $cols            = array(
                'order_id'           => 'order_id',
                'product_id'         => 'product_id',
                'product_type'       => 'product_type',
                'created_at'         => 'created_at',
                'sku'                => 'sku',
                'total_child_cost_amount'  => new \Zend_Db_Expr('SUM(base_cost)'),
                'total_qty_ordered'  => new \Zend_Db_Expr("SUM(qty_ordered - {$qtyCanceledExpr})"),
                'total_qty_invoiced' => new \Zend_Db_Expr('SUM(qty_invoiced)'),
                'total_qty_shipping' => new \Zend_Db_Expr('SUM(qty_shipped)'),
                'total_qty_refunded' => new \Zend_Db_Expr('SUM(qty_refunded)'),
                'total_item_cost'    => new \Zend_Db_Expr('SUM(row_total)'),
                'total_parent_cost_amount' => 'sales_item2.total_parent_cost_amount',
                'total_cost_amount'            => new \Zend_Db_Expr(
                    sprintf(' (%s + %s) ',
                        $adapter->getIfNullSql('SUM(base_cost)', 0),
                        $adapter->getIfNullSql('sales_item2.total_parent_cost_amount',0)
                    )
                )
            );

            $selectOrderItem->from(array('sales_item1' => $this->getTable('sales_order_item')), $cols)
                ->where('parent_item_id IS NULL')
                ->joinLeft(array('sales_item2' => $selectOrderItem1), 'sales_item1.order_id = sales_item2.order_id', array())
                ->group('sales_item1.order_id', 'sales_item1.product_id', 'sales_item1.product_type', 'sales_item1.created_at', 'sales_item1.sku');

            $this->getSelect()->columns($columns)
                ->join(array('oi' => $selectOrderItem), 'oi.order_id = main_table.entity_id', array());
            if($aggregationField) {
                $this->getSelect()->group($aggregationField);
            }

            $this->join(['currency_rate' => 'directory_currency_rate'], "main_table.order_currency_code=currency_rate.currency_to AND currency_rate.currency_from='{$this->_order_rate}'",
                ['rate']);

        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }


    /**
     * Aggregate Orders data by custom field
     *
     * @throws Exception
     * @param string $aggregationField
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Resource_Report_Order_Createdat
     */
    protected function _aggregateOrderItemsByField($aggregationField = "", $hide_fields = array(), $show_fields = array())
    {
        $adapter = $this->getResource()->getConnection();
        try {

            $subSelect = null;
            $qtyCanceledExpr = $adapter->getIfNullSql('oi.qty_canceled', 0);
            // Columns list
            $columns = array(
                // convert dates from UTC to current admin timezone
                'rate'                         => 'currency_rate.rate',
                'oi.*'                           => 'oi.*',
                'increment_id'                   => 'main_table.increment_id',
                'status'                         => 'main_table.status',
                'created_at'                     => 'main_table.created_at',
                'store_id'                       => 'main_table.store_id',
                'order_status'                   => 'main_table.status',
                'real_tax_refunded'              => new \Zend_Db_Expr("IFNULL(oi.tax_refunded / currency_rate.rate,0)"),
                'real_qty_shipped'               => new \Zend_Db_Expr("IFNULL(oi.qty_shipped,0)"),
                'real_qty_refunded'              => new \Zend_Db_Expr("IFNULL(oi.qty_refunded,0)"),
                'real_qty_ordered'               => new \Zend_Db_Expr("(oi.qty_ordered - {$qtyCanceledExpr})"),
                'price'                          => new \Zend_Db_Expr("(price / currency_rate.rate)"),
                'subtotal'                       => new \Zend_Db_Expr("((oi.qty_ordered * subtotal) / currency_rate.rate)"),
                'discount_amount'                => new \Zend_Db_Expr("(oi.discount_amount / currency_rate.rate)"),
                'tax_amount'                     => new \Zend_Db_Expr("(oi.tax_amount / currency_rate.rate)"),
                'tax_invoiced'                   => new \Zend_Db_Expr("(oi.tax_invoiced / currency_rate.rate)"),
                'row_total'                      => new \Zend_Db_Expr("(oi.row_total / currency_rate.rate)"),
                'row_total_incl_tax'             => new \Zend_Db_Expr("(oi.row_total_incl_tax / currency_rate.rate)"),
                'row_invoiced'                   => new \Zend_Db_Expr("(oi.row_invoiced / currency_rate.rate)"),
                'amount_refunded'                => new \Zend_Db_Expr("(oi.amount_refunded / currency_rate.rate)"),
                'total_cost_amount'              => new \Zend_Db_Expr("(oi.total_cost_amount / currency_rate.rate)"),
                'total_revenue_amount'           => new \Zend_Db_Expr(
                    sprintf('(CASE WHEN oi.base_row_invoiced > 0 THEN IFNULL((%s - %s - %s - %s - %s) / currency_rate.rate,0) ELSE 0 END)',
                        $adapter->getIfNullSql('oi.base_row_invoiced', 0),
                        $adapter->getIfNullSql('oi.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('oi.discount_amount', 0),
                        $adapter->getIfNullSql('oi.base_amount_refunded', 0),
                        $adapter->getIfNullSql('oi.base_tax_refunded', 0)
                    )
                ),
                'row_refunded_incl_tax'           => new \Zend_Db_Expr(
                    sprintf('(%s + %s) / currency_rate.rate',
                        $adapter->getIfNullSql('oi.amount_refunded', 0),
                        $adapter->getIfNullSql('oi.tax_refunded', 0)
                    )
                ),
                'row_invoiced_incl_tax'           => new \Zend_Db_Expr(
                    sprintf('(%s + %s) / currency_rate.rate',
                        $adapter->getIfNullSql('oi.row_invoiced', 0),
                        $adapter->getIfNullSql('oi.tax_invoiced', 0)
                    )
                ),
                'total_revenue_amount_excl_tax'           => new \Zend_Db_Expr(
                    sprintf('(CASE WHEN oi.base_row_invoiced > 0 THEN IFNULL((%s - %s - %s) / %s,0) ELSE 0 END)',
                        $adapter->getIfNullSql('oi.base_row_invoiced', 0),
                        $adapter->getIfNullSql('oi.discount_amount', 0),
                        $adapter->getIfNullSql('oi.base_amount_refunded', 0),
                        $adapter->getIfNullSql('currency_rate.rate', 0)
                    )
                ),
                'total_profit_amount'           => new \Zend_Db_Expr(
                    sprintf('(CASE WHEN oi.base_row_invoiced > 0 THEN IFNULL(((%s - %s - %s - %s - %s) - (%s * %s)) / currency_rate.rate,0) ELSE 0 END)',
                        $adapter->getIfNullSql('oi.base_row_invoiced', 0),
                        $adapter->getIfNullSql('oi.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('oi.discount_amount', 0),
                        $adapter->getIfNullSql('oi.base_amount_refunded', 0),
                        $adapter->getIfNullSql('oi.base_tax_refunded', 0),
                        $adapter->getIfNullSql('oi.qty_ordered', 0),
                        $adapter->getIfNullSql('oi.base_cost', 0)
                    )
                ),
                'total_margin'           => new \Zend_Db_Expr(
                    sprintf('(CASE WHEN oi.base_row_invoiced > 0 THEN IFNULL(ROUND((((%s - %s - %s -  %s - %s) - (%s * %s))/(%s - %s - %s -  %s - %s))*100), 100) ELSE 0 END)',
                        $adapter->getIfNullSql('oi.base_row_invoiced', 0),
                        $adapter->getIfNullSql('oi.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('oi.discount_amount', 0),
                        $adapter->getIfNullSql('oi.base_amount_refunded', 0),
                        $adapter->getIfNullSql('oi.base_tax_refunded', 0),
                        $adapter->getIfNullSql('oi.qty_ordered', 0),
                        $adapter->getIfNullSql('oi.base_cost', 0),
                        $adapter->getIfNullSql('oi.base_row_invoiced', 0),
                        $adapter->getIfNullSql('oi.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('oi.discount_amount', 0),
                        $adapter->getIfNullSql('oi.base_amount_refunded', 0),
                        $adapter->getIfNullSql('oi.base_tax_refunded', 0)
                    )
                ),
            );

            if($hide_fields) {
                foreach($hide_fields as $field){
                    if(isset($columns[$field])){
                        unset($columns[$field]);
                    }
                }
            }

            $selectOrderItem = $adapter->select();

            $cols1 = array(
                'order_id'           => 'order_id',
                'total_parent_cost_amount'  => new \Zend_Db_Expr($adapter->getIfNullSql('SUM(base_cost)',0)),
            );
            $selectOrderItem1 = $adapter->select()->from($this->getTable('sales_order_item'), $cols1)->where('parent_item_id IS NOT NULL')->group('order_id');
            $cols            = array(
                'sales_item1.*'           => 'sales_item1.*',
                'total_parent_cost_amount' => 'sales_item2.total_parent_cost_amount',
                'total_cost_amount'            => new \Zend_Db_Expr(
                    sprintf(' (%s + %s) ',
                        $adapter->getIfNullSql('SUM(base_cost)', 0),
                        $adapter->getIfNullSql('sales_item2.total_parent_cost_amount',0)
                    )
                )

            );

            $selectOrderItem->from(array('sales_item1' => $this->getTable('sales_order_item')), $cols)
                ->where('parent_item_id IS NULL')
                ->joinLeft(array('sales_item2' => $selectOrderItem1), 'sales_item1.order_id = sales_item2.order_id', array())
                ->group('sales_item1.order_id');
            $this->getSelect()->columns($columns)
                ->join(array('oi' => $selectOrderItem), 'oi.order_id = main_table.entity_id', array());

            if($aggregationField) {
                $this->getSelect()->group($aggregationField);
            }
            $this->join(['currency_rate' => 'directory_currency_rate'], "main_table.order_currency_code=currency_rate.currency_to AND currency_rate.currency_from='{$this->_order_rate}'",
                ['rate']);
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

}

