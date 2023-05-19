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
interface SalesproductcountryInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PERIOD = 'period';

    const ORDERS_COUNT = 'orders_count';

    const TOTAL_SUBTOTAL_AMOUNT = 'total_subtotal_amount';

    const SUBTOTAL_CURRENCY = 'subtotal_currency';

    const TOTAL_QTY_ORDERED = 'total_qty_ordered';

    const TOTAL_TAX_AMOUNT = 'total_tax_amount';

    const TAX_CURRENCY = 'tax_currency';

    const TOTAL_SHIPPING_AMOUNT = 'total_shipping_amount';

    const SHIPPING_CURRENCY = 'shipping_currency';

    const TOTAL_DISCOUNT_AMOUNT = 'total_discount_amount';

    const DISCOUNT_CURRENCY = 'discount_currency';

    const TOTAL_GRANDTOTAL_AMOUNT = 'total_grandtotal_amount';

    const GRANDTOTAL_CURRENCY = 'grandtotal_currency';

    const TOTAL_INVOICED_AMOUNT = 'total_invoiced_amount';

    const INVOICED_CURRENCY = 'invoiced_currency';

    const TOTAL_REFUNDED_AMOUNT = 'total_refunded_amount';

    const REFUNDED_CURRENCY = 'refunded_currency';

    const TOTAL_REVENUE_AMOUNT = 'total_revenue_amount';

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
     * revenue_currency
     *
     * @return string
     */
    public function getRevenueCurrency();

    /**
     * Set revenue_currency
     *
     * @param string $revenue_currency
     * @return $this
     */
    public function setRevenueCurrency($revenue_currency);
    
}
