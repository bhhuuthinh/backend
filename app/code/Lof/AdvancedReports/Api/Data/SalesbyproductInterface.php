<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\AdvancedReports\Api\Data;

/**
 * @api
 */
interface SalesbyproductInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@-*/

    /**
     * Period
     *
     * @return string
     */
    public function getPeriod();

    /**
     * Set period
     *
     * @param string $period
     * @return $this
     */
    public function setPeriod($period);

    /**
     * period_label
     *
     * @return string
     */
    public function getPeriodLabel();

    /**
     * Set period_label
     *
     * @param string $period_label
     * @return $this
     */
    public function setPeriodLabel($period_label);

    /**
     * orders_count
     *
     * @return string
     */
    public function getOrdersCount();

    /**
     * Set orders_count
     *
     * @param string $orders_count
     * @return $this
     */
    public function setOrdersCount($orders_count);

    /**
     * total_qty
     *
     * @return string
     */
    public function getTotalQty();

    /**
     * Set total_qty
     *
     * @param string $total_qty
     * @return $this
     */
    public function setTotalQty($total_qty);

    /**
     * total_revenue_amount
     *
     * @return string
     */
    public function getTotalRevenueAmount();

    /**
     * Set total_revenue_amount
     *
     * @param string $total_revenue_amount
     * @return $this
     */
    public function setTotalRevenueAmount($total_revenue_amount);

    /**
     * total_revenue_currency
     *
     * @return string
     */
    public function getTotalRevenueCurrency();

    /**
     * Set total_revenue_currency
     *
     * @param string $total_revenue_currency
     * @return $this
     */
    public function setTotalRevenueCurrency($total_revenue_currency);
    
}
