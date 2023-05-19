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

namespace Lof\AdvancedReports\Model\ResourceModel\Earning;

class Collection extends \Lof\AdvancedReports\Model\ResourceModel\AbstractReport\Ordercollection {

    protected $_date_column_filter = "main_table.created_at";

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
     * @param $year
     * @return $this
     */
    public function addYearFilter($year)
    {
        $this->_year_filter = $year;

        return $this;
    }

    /**
     * @param $month
     * @return $this
     */
    public function addMonthFilter($month)
    {
        $this->_month_filter = $month;

        return $this;
    }

    /**
     * @param $day
     * @return $this
     */
    public function addDayFilter($day)
    {
        $this->_day_filter = $day;

        return $this;
    }

    protected function _applyDateFilter()
    {
        $select_datefield = [];
        if ($this->_year_filter) {
            $select_datefield = [
                'period' => 'MONTH(' . $this->getDateColumnFilter() . ')',
                $this->getDateColumnFilter(),
            ];

            $this->getSelect()->where('YEAR(' . $this->getDateColumnFilter() . ") = ?", $this->_year_filter);

        } else {
            $select_datefield = [
                'period' => 'YEAR(' . $this->getDateColumnFilter() . ')',
                $this->getDateColumnFilter(),
            ];
        }

        if ($this->_month_filter) {
            $select_datefield = [
                'period' => 'DAY(' . $this->getDateColumnFilter() . ')',
                $this->getDateColumnFilter(),
            ];

            $this->getSelect()->where('MONTH(' . $this->getDateColumnFilter() . ") = ?", $this->_month_filter);
        }

        if ($this->_day_filter) {
            $select_datefield = [
                'period' => 'HOUR(' . $this->getDateColumnFilter() . ')',
                $this->getDateColumnFilter(),
            ];
            $this->getSelect()->where('DAY(' . $this->getDateColumnFilter() . ") = ?", $this->_day_filter);
        }

        if ($select_datefield) {
            $this->getSelect()->columns($select_datefield);
        }

        return $this;
    }

    public function prepareReportCollection()
    {
        $adapter = $this->getResource()->getConnection();

        $this->setMainTable('sales_order');
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->getSelect()->columns([
            'rate'                  => 'currency_rate.rate',
            'order_ids'             => 'GROUP_CONCAT(DISTINCT main_table.entity_id SEPARATOR \',\')',
            'orders_count'          => 'COUNT(main_table.entity_id)',
            'total_revenue_amount1' => 'SUM(main_table.total_paid / currency_rate.rate)',
            'total_revenue_amount'  => new \Zend_Db_Expr(
                sprintf('SUM((%s - %s - %s - (%s - %s - %s)) / currency_rate.rate)',
                    $adapter->getIfNullSql('main_table.total_invoiced', 0),
                    $adapter->getIfNullSql('main_table.tax_invoiced', 0),
                    $adapter->getIfNullSql('main_table.shipping_invoiced', 0),
                    $adapter->getIfNullSql('main_table.total_refunded', 0),
                    $adapter->getIfNullSql('main_table.tax_refunded', 0),
                    $adapter->getIfNullSql('main_table.shipping_refunded', 0)
                )
            ),
            'total_item_count'      => 'SUM(main_table.total_item_count)',
            'total_qty_ordered'     => 'SUM(main_table.total_qty_ordered)',
        ]);

        $this->join(['currency_rate' => 'directory_currency_rate'], "main_table.order_currency_code=currency_rate.currency_to AND currency_rate.currency_from='{$this->_order_rate}'",
            ['rate']);

        return $this;
    }

    public function prepareBestsellersCollection()
    {
        $this->setMainTable('sales_order');
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->getSelect()->columns([
            'rate'                 => 'currency_rate.rate',
            'order_ids'            => 'GROUP_CONCAT(DISTINCT main_table.entity_id SEPARATOR \',\')',
            'qty_ordered'          => 'SUM(order_item.qty_ordered)',
            'orders_count'         => 'COUNT(main_table.entity_id)',
            'total_revenue_amount' => 'SUM(main_table.total_paid)',
            'total_item_count'     => 'SUM(main_table.total_item_count)',
            'total_qty_ordered'    => 'SUM(main_table.total_qty_ordered)',
            'base_row_total'       => 'SUM(order_item.base_row_total)',
            'product_id'           => 'order_item.product_id',
            'product_name'         => 'MAX(order_item.name)',
            'product_price'        => 'MAX(order_item.price)',
        ]);

        $this->join(['currency_rate' => 'directory_currency_rate'], "main_table.order_currency_code=currency_rate.currency_to AND currency_rate.currency_from='{$this->_order_rate}'",
            ['rate']);

        return $this;
    }

    public function prepareCountryReport()
    {
        $this->setMainTable('sales_order');
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $this->getSelect()->columns([
            'rate'                 => 'currency_rate.rate',
            'order_ids'            => 'GROUP_CONCAT(DISTINCT main_table.entity_id SEPARATOR \',\')',
            'orders_count'         => 'COUNT(main_table.entity_id)',
            'total_revenue_amount' => 'SUM(main_table.total_paid)',
            'total_item_count'     => 'SUM(main_table.total_item_count)',
            'total_qty_ordered'    => 'SUM(main_table.total_qty_ordered)',
        ]);

        $this->join(['currency_rate' => 'directory_currency_rate'], "main_table.order_currency_code=currency_rate.currency_to AND currency_rate.currency_from='{$this->_order_rate}'",
            ['rate']);

        return $this;
    }

    public function applyCustomFilter()
    {
        $this->_applyDateFilter();
        $this->_applyStoresFilter();
        $this->_applyOrderStatusFilter();

        return $this;
    }
}
