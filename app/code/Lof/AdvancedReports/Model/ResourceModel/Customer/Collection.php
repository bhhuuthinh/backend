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

namespace Lof\AdvancedReports\Model\ResourceModel\Customer;
class  Collection extends \Lof\AdvancedReports\Model\ResourceModel\AbstractReport\Ordercollection
{
    protected $_date_column_filter = "main_table.created_at";
    protected $_period_type = "";
    protected $_date_period_field = null;

    public function setPeriodDateField($period_field = array()){
        $this->_date_period_field = $period_field;
        return $this;
    }
  /**
     * Is live
     *
     * @var boolean
     */
    protected $_isLive   = false;


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
            $this->setPeriodDateField($select_datefield);
            $this->getSelect()->columns($select_datefield);
        }
        // sql theo filter date
        if($this->_to_date_filter && $this->_from_date_filter) {

            // kiem tra lai doan convert ngay thang nay !

            $dateStart = $this->convertConfigTimeToUtc($this->_from_date_filter,'Y-m-d 00:00:00');
            $endStart = $this->convertConfigTimeToUtc($this->_to_date_filter, 'Y-m-d 23:59:59');
            $dateRange = array('from' => $dateStart, 'to' => $endStart , 'datetime' => true);

            $this->addFieldToFilter($this->getDateColumnFilter(), $dateRange);
        }


        return $this;
    }
    public function filterByCustomerEmail($customer_email = "") {
        if($customer_email){
            $this->addFieldToFilter("customer_email", $customer_email);
        }
        return $this;
    }
     public function prepareCustomerActivityCollection() {
        $hide_fields = array();
        $this->setMainTable('customer_entity');
        $this->_aggregateActivityByField('period', $hide_fields, array(), false);
        return $this;
    }

    public function prepareCustomerscityCollection() {
        $hide_fields = array();
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->_aggregateLocationByField('city', $hide_fields);
        return $this;
    }

    public function prepareCustomerscountryCollection() {
        $hide_fields = array();
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->_aggregateLocationByField('country_id', $hide_fields);
        return $this;
    }

    public function prepareCustomersreportCollection() {
        $hide_fields = array();
        $this->_aggregateByField('main_table.customer_id', $hide_fields);
        return $this;
    }

    public function prepareTopcustomerCollection() {
        $hide_fields = array();
        $this->_aggregateByField('main_table.customer_id', $hide_fields, array(), false);
        return $this;
    }

    public function prepareProductscustomerCollection() {
        $hide_fields = array();
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->_aggregateByField(null, $hide_fields, array(), false);
        $columns = array(
                            'total_qty_ordered'              => 'total_qty_ordered',
                            'total_customer'                 => new \Zend_Db_Expr('COUNT(main_table.customer_id)')
                            );
        $this->getSelect()->columns($columns)
                ->group( 'main_table.total_qty_ordered' );

        return $this;
    }


    public function applyCustomFilter() {
        $this->_applyDateFilter();
        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();
        return $this;
    }


    public function applyCustomFilterNew() {
        $this->_applyDateFilter();
        $this->_applyStoresFilter();
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
    protected function _aggregateActivityByField($aggregationField, $hide_fields = array(), $show_fields = array())
    {
        $adapter = $this->getResource()->getConnection();
        try {

            $subSelect = null;
            // Columns list
            $columns = array(
                // convert dates from UTC to current admin timezone
                'store_id'                       => 'main_table.store_id',
                'new_accounts_count'             => new \Zend_Db_Expr('COUNT(main_table.entity_id)')
            );

            if($hide_fields) {
                foreach($hide_fields as $field){
                    if(isset($columns[$field])){
                        unset($columns[$field]);
                    }
                }
            }
            $columns['store_id']       = new \Zend_Db_Expr($this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId());

            $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
            $this->getSelect()->columns($columns);
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }
    /**
    * @function Join Customer Orders Collection
    *
    *
    **/
    public function joinCustomerOrders(\Magento\Framework\DB\Select $selectOrder ) {
        $this->getSelect()->columns(array('orders_count' => 'IFNULL(o.orders_count,0)'))->joinLeft(array('o' => $selectOrder), 'o.period = '.$this->_date_period_field['period'], array());
        return $this;
    }
    /**
    * @function Join Customer Reviews Collection
    *
    *
    **/
    public function joinCustomerReviews(\Magento\Framework\DB\Select $selectReview ) {
        $this->getSelect()->columns(array('reviews_count' => 'IFNULL(r.reviews_count,0)'))->joinLeft(array('r' => $selectReview), 'r.period = '.$this->_date_period_field['period'], array());
        return $this;
    }
    /**
    * @function Join New Accounts with Orders Collection
    *
    *
    **/
    public function joinNewAccountOrders(\Magento\Framework\DB\Select $selectOrder, $group_by = 'city' ) {
        $this->getSelect()->columns(array('new_accounts_orders_count' => 'IFNULL(o.new_accounts_orders_count, 0)'))->joinLeft(array('o' => $selectOrder), 'o.'.$group_by.' = oadd.'.$group_by, array());
        // $this->getSelect()->columns(array('new_accounts_orders_count' => 'IFNULL(COUNT(main_table.entity_id), 0)'));
        return $this;
    }
    /**
    * @function Join New Accounts Collection
    *
    *
    **/
    public function joinNewAccounts(\Magento\Framework\DB\Select $selectOrder, $group_by = 'city' ) {
        $this->getSelect()->columns(array('new_accounts_count' => 'IFNULL(nc.new_accounts_count,0)'))->joinLeft(array('nc' => $selectOrder), 'nc.'.$group_by.' = oadd.'.$group_by, array());
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
    protected function _aggregateByField($aggregationField = "", $hide_fields = array(), $show_fields = array(), $join_address = true)
    {
        $adapter = $this->getResource()->getConnection();
        try {

            $subSelect = null;
            // Columns list
            $columns = array(
                // convert dates from UTC to current admin timezone
                'store_id'                       => 'main_table.store_id',
                'customer_id'                    => 'main_table.customer_id',
                'order_status'                   => 'main_table.status',
                'customer_name'                  => 'CONCAT(main_table.customer_firstname," ",main_table.customer_lastname)',
                'orders_count'                   => new \Zend_Db_Expr('COUNT(DISTINCT main_table.entity_id)'),
                'total_subtotal_amount'          => new \Zend_Db_Expr('SUM(main_table.subtotal)'),
                'total_grandtotal_amount'        => new \Zend_Db_Expr('SUM(main_table.grand_total)'),
                'total_income_amount'            => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s) )',
                        $adapter->getIfNullSql('main_table.base_grand_total', 0),
                        $adapter->getIfNullSql('main_table.base_total_canceled',0)
                    )
                ),
                'total_revenue_amount'           => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s - %s - (%s - %s - %s)) )',
                        $adapter->getIfNullSql('main_table.base_total_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_total_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_refunded', 0)
                    )
                ),
                'total_profit_amount'            => new \Zend_Db_Expr(
                    sprintf('SUM(((%s - %s) - (%s - %s) - (%s - %s) - %s) )',
                        $adapter->getIfNullSql('main_table.base_total_paid', 0),
                        $adapter->getIfNullSql('main_table.base_total_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_total_invoiced_cost', 0)
                    )
                ),
                'total_invoiced_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getIfNullSql('main_table.base_total_invoiced', 0)
                    )
                ),
                'total_canceled_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getIfNullSql('main_table.base_total_canceled', 0)
                    )
                ),
                'total_paid_amount'              => new \Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getIfNullSql('main_table.base_total_paid', 0)
                    )
                ),
                'total_refunded_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getIfNullSql('main_table.base_total_refunded', 0)
                    )
                ),
                'total_tax_amount'               => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_tax_amount', 0),
                        $adapter->getIfNullSql('main_table.base_tax_canceled', 0)
                    )
                ),
                'total_tax_amount_actual'        => new \Zend_Db_Expr(
                    sprintf('SUM((%s -%s))',
                        $adapter->getIfNullSql('main_table.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_tax_refunded', 0)
                    )
                ),
                'total_shipping_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_shipping_amount', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_canceled', 0)
                    )
                ),
                'total_shipping_amount_actual'   => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_refunded', 0)
                    )
                ),
                'total_discount_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM((ABS(%s) - %s))',
                        $adapter->getIfNullSql('main_table.base_discount_amount', 0),
                        $adapter->getIfNullSql('main_table.base_discount_canceled', 0)
                    )
                ),
                'total_discount_amount_actual'   => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_discount_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_discount_refunded', 0)
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
            $selectaddress         = $adapter->select();
            $colsaddress           = array(
                "customer_address_id" =>    "customer_address_id",
                "region"              =>    "region",
                "postcode"            =>    "postcode",
                "street"              =>    "street",
                "city"                =>    "city",
                "country_id"          =>    "country_id",
                "parent_id"           =>    "parent_id"
                );
            $selectaddress->from($this->getTable('sales_order_address'), $colsaddress)
                ->group('parent_id');
                // ->distinct(true);
            $this->getSelect()->columns($columns)
                ->join(array('c' => $this->getTable('customer_entity')), 'c.entity_id = main_table.customer_id', array());
            if($join_address){
                $this->getSelect()->join(array('oadd' => $selectaddress), 'oadd.parent_id = main_table.entity_id',  array("region","postcode","street","city","country_id"));
            }

            $this->getSelect()->where('main_table.state NOT IN (?)', array(
                    \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT,
                ));

            if($aggregationField) {
                $this->getSelect()->group($aggregationField);
            }

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
    protected function _aggregateLocationByField($aggregationField = "", $hide_fields = array(), $show_fields = array(), $join_address = true)
    {
        $adapter = $this->getResource()->getConnection();
        try {

            $subSelect = null;
            // Columns list
            $columns = array(
                // convert dates from UTC to current admin timezone
                'store_id'                       => 'main_table.store_id',
                'order_status'                   => 'main_table.status',
                'total_qty_ordered'              => new \Zend_Db_Expr('SUM(oi.total_qty_ordered)'),
                'total_qty_invoiced'             => new \Zend_Db_Expr('SUM(oi.total_qty_invoiced)'),
                'orders_count'                   => new \Zend_Db_Expr('COUNT(main_table.entity_id)'),
                'total_subtotal_amount'          => new \Zend_Db_Expr('SUM(main_table.subtotal)'),
                'total_grandtotal_amount'        => new \Zend_Db_Expr('SUM(main_table.grand_total)'),
                'total_income_amount'            => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_grand_total', 0),
                        $adapter->getIfNullSql('main_table.base_total_canceled',0)
                    )
                ),
                'total_revenue_amount'           => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s - %s - (%s - %s - %s)))',
                        $adapter->getIfNullSql('main_table.base_total_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_total_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_refunded', 0)
                    )
                ),
                'total_profit_amount'            => new \Zend_Db_Expr(
                    sprintf('SUM(((%s - %s) - (%s - %s) - (%s - %s) - %s))',
                        $adapter->getIfNullSql('main_table.base_total_paid', 0),
                        $adapter->getIfNullSql('main_table.base_total_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_tax_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_refunded', 0),
                        $adapter->getIfNullSql('main_table.base_total_invoiced_cost', 0)
                    )
                ),
                'total_invoiced_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getIfNullSql('main_table.base_total_invoiced', 0)
                    )
                ),
                'total_canceled_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getIfNullSql('main_table.base_total_canceled', 0)
                    )
                ),
                'total_paid_amount'              => new \Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getIfNullSql('main_table.base_total_paid', 0)
                    )
                ),
                'total_refunded_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM(%s)',
                        $adapter->getIfNullSql('main_table.base_total_refunded', 0)
                    )
                ),
                'total_tax_amount'               => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_tax_amount', 0),
                        $adapter->getIfNullSql('main_table.base_tax_canceled', 0)
                    )
                ),
                'total_tax_amount_actual'        => new \Zend_Db_Expr(
                    sprintf('SUM((%s -%s))',
                        $adapter->getIfNullSql('main_table.base_tax_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_tax_refunded', 0)
                    )
                ),
                'total_shipping_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_shipping_amount', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_canceled', 0)
                    )
                ),
                'total_shipping_amount_actual'   => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_shipping_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_shipping_refunded', 0)
                    )
                ),
                'total_discount_amount'          => new \Zend_Db_Expr(
                    sprintf('SUM((ABS(%s) - %s))',
                        $adapter->getIfNullSql('main_table.base_discount_amount', 0),
                        $adapter->getIfNullSql('main_table.base_discount_canceled', 0)
                    )
                ),
                'total_discount_amount_actual'   => new \Zend_Db_Expr(
                    sprintf('SUM((%s - %s))',
                        $adapter->getIfNullSql('main_table.base_discount_invoiced', 0),
                        $adapter->getIfNullSql('main_table.base_discount_refunded', 0)
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

            $qtyCanceledExpr = $adapter->getIfNullSql('qty_canceled', 0);
            $cols            = array(
                'order_id'           => 'order_id',
                'product_type'       => 'product_type',
                'total_qty_ordered'  => new \Zend_Db_Expr("SUM(qty_ordered - {$qtyCanceledExpr})"),
                'total_qty_invoiced' => new \Zend_Db_Expr('SUM(qty_invoiced)'),
                'total_item_cost'    => new \Zend_Db_Expr('SUM(row_total)'),
            );

            $selectOrderItem->from($this->getTable('sales_order_item'), $cols)
                ->where('parent_item_id IS NULL')
                ->group('order_id');


            $selectaddress         = $adapter->select();
            $colsaddress           = array(
                "customer_address_id" =>    "customer_address_id",
                "region"              =>    "region",
                "postcode"            =>    "postcode",
                "street"              =>    "street",
                "city"                =>    "city",
                "country_id"          =>    "country_id",
                "parent_id"           =>    "parent_id"
                );
            $selectaddress->from( $this->getTable('sales_order_address'), $colsaddress)
            ->group('parent_id');
            // ->distinct(true);

            $this->getSelect()->columns($columns)
                ->join(array('oi' => $selectOrderItem), 'oi.order_id = main_table.entity_id', array())
                ->join(array('c' => $this->getTable('customer_entity')), 'c.entity_id = main_table.customer_id', array());
            if($join_address){
                $this->getSelect()->join(array('oadd' => $selectaddress), 'oadd.parent_id = main_table.entity_id',  array("region","postcode","street","city","country_id"));
            }

            $this->getSelect()->where('main_table.state NOT IN (?)', array(
                    \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT,
                ));
            if($aggregationField) {
                $this->getSelect()->group($aggregationField);
            }
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

}
