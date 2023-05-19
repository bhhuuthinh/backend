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
interface EarningInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PERIOD = 'period';

    const ORDERS_COUNT = 'orders_count';

    const TOTAL_ITEM_COUNT = 'total_item_count';

    const TOTAL_QTY_ORDERED = 'total_qty_ordered';

    const TOTAL_REVENUE_AMOUNT = 'total_revenue_amount';

    const PERIOD_LABEL= 'period_label';

    const REVENUE_CURRENCY = 'revenue_currency';

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
     * Period Label
     *
     * @return string
     */
    public function getPeriodLabel();

    /**
     * Set period label
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
     * total_item_count
     *
     * @return string
     */
    public function getTotalItemCount();

    /**
     * Set total_item_count
     *
     * @param string $total_item_count
     * @return $this
     */
    public function setTotalItemCount($total_item_count);

    /**
     * total_qty_ordered
     *
     * @return string
     */
    public function getTotalQtyOrdered();

    /**
     * Set total_qty_ordered
     *
     * @param string $total_qty_ordered
     * @return $this
     */
    public function setTotalQtyOrdered($total_qty_ordered);

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
     * Revenue currency
     *
     * @return string
     */
    public function getRevenueCurrency();

    /**
     * Set revenue currency
     *
     * @param string $revenue_currency
     * @return $this
     */
    public function setRevenueCurrency($revenue_currency);
    
}
