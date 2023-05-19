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
interface OverviewInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PERIOD = 'period';

    const PERIOD_LABEL= 'period_label';

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
     * Set total_subtotal_amount
     *
     * @param string $total_subtotal_amount
     * @return $this
     */
    public function setTotalSubtotalAmount($total_subtotal_amount);

    /**
     * total_subtotal_amount
     *
     * @return string
     */
    public function getTotalSubtotalAmount();

    /**
     * Set subtotal_currency
     *
     * @param string $subtotal_currency
     * @return $this
     */
    public function setSubtotalCurrency($subtotal_currency);

    /**
     * subtotal_currency
     *
     * @return string
     */
    public function getSubtotalCurrency();


    /**
     * total_tax_amount
     *
     * @return string
     */
    public function getTotalTaxAmount();

    /**
     * Set total_tax_amount
     *
     * @param string $total_tax_amount
     * @return $this
     */
    public function setTotalTaxAmount($total_tax_amount);

    /**
     * tax_currency
     *
     * @return string
     */
    public function getTaxCurrency();

    /**
     * Set tax_currency
     *
     * @param string $tax_currency
     * @return $this
     */
    public function setTaxCurrency($tax_currency);

    /**
     * total_shipping_amount
     *
     * @return string
     */
    public function getTotalShippingAmount();

    /**
     * Set total_shipping_amount
     *
     * @param string $total_shipping_amount
     * @return $this
     */
    public function setTotalShippingAmount($total_shipping_amount);

    /**
     * shipping_currency
     *
     * @return string
     */
    public function getShippingCurrency();

    /**
     * Set shipping_currency
     *
     * @param string $shipping_currency
     * @return $this
     */
    public function setShippingCurrency($shipping_currency);


    /**
     * total_discount_amount
     *
     * @return string
     */
    public function getTotalDiscountAmount();

    /**
     * Set total_discount_amount
     *
     * @param string $total_discount_amount
     * @return $this
     */
    public function setTotalDiscountAmount($total_discount_amount);

    /**
     * discount_currency
     *
     * @return string
     */
    public function getDiscountCurrency();

    /**
     * Set discount_currency
     *
     * @param string $discount_currency
     * @return $this
     */
    public function setDiscountCurrency($discount_currency);

     /**
     * total_grandtotal_amount
     *
     * @return string
     */
    public function getTotalGrandtotalAmount();

    /**
     * Set total_grandtotal_amount
     *
     * @param string $total_grandtotal_amount
     * @return $this
     */
    public function setTotalGrandtotalAmount($total_grandtotal_amount);

    /**
     * grandtotal_currency
     *
     * @return string
     */
    public function getGrandtotalCurrency();

    /**
     * Set grandtotal_currency
     *
     * @param string $grandtotal_currency
     * @return $this
     */
    public function setGrandtotalCurrency($grandtotal_currency);

     /**
     * total_invoiced_amount
     *
     * @return string
     */
    public function getTotalInvoicedAmount();

    /**
     * Set total_invoiced_amount
     *
     * @param string $total_invoiced_amount
     * @return $this
     */
    public function setTotalInvoicedAmount($total_invoiced_amount);

    /**
     * invoiced_currency
     *
     * @return string
     */
    public function getInvoicedCurrency();

    /**
     * Set invoiced_currency
     *
     * @param string $invoiced_currency
     * @return $this
     */
    public function setInvoicedCurrency($invoiced_currency);

    /**
     * total_refunded_amount
     *
     * @return string
     */
    public function getTotalRefundedAmount();

    /**
     * Set total_refunded_amount
     *
     * @param string $total_refunded_amount
     * @return $this
     */
    public function setTotalRefundedAmount($total_refunded_amount);

    /**
     * refunded_currency
     *
     * @return string
     */
    public function getRefundedCurrency();

    /**
     * Set refunded_currency
     *
     * @param string $refunded_currency
     * @return $this
     */
    public function setRefundedCurrency($refunded_currency);

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
