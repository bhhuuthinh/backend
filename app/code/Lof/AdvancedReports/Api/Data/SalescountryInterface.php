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
interface SalescountryInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@-*/

    /**
     * country_id
     *
     * @return string
     */
    public function getCountryId();

    /**
     * Set country_id
     *
     * @param string $country_id
     * @return $this
     */
    public function setCountryId($country_id);

    /**
     * country_label
     *
     * @return string
     */
    public function getCountryLabel();

    /**
     * Set country_label
     *
     * @param string $country_label
     * @return $this
     */
    public function setCountryLabel($country_label);


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
     * total_subtotal_amount
     *
     * @return string
     */
    public function getTotalSubtotalAmount();

    /**
     * Set total_subtotal_amount
     *
     * @param string $total_subtotal_amount
     * @return $this
     */
    public function setTotalSubtotalAmount($total_subtotal_amount);

    /**
     * total_subtotal_currency
     *
     * @return string
     */
    public function getTotalSubtotalCurrency();

    /**
     * Set total_subtotal_currency
     *
     * @param string $total_subtotal_currency
     * @return $this
     */
    public function setTotalSubtotalCurrency($total_subtotal_currency);

    /**
     * get total_discount_amount
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
     * total_discount_currency
     *
     * @return string
     */
    public function getTotalDiscountCurrency();

    /**
     * Set total_discount_currency
     *
     * @param string $total_discount_currency
     * @return $this
     */
    public function setTotalDiscountCurrency($total_discount_currency);

    /**
     * get total_grandtotal_amount
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
     * total_grandtotal_currency
     *
     * @return string
     */
    public function getTotalGrandtotalCurrency();

    /**
     * Set total_grandtotal_currency
     *
     * @param string $total_grandtotal_currency
     * @return $this
     */
    public function setTotalGrandtotalCurrency($total_grandtotal_currency);

    /**
     * get total_invoiced_amount
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
     * total_invoiced_currency
     *
     * @return string
     */
    public function getTotalInvoicedCurrency();

    /**
     * Set total_invoiced_currency
     *
     * @param string $total_invoiced_currency
     * @return $this
     */
    public function setTotalInvoicedCurrency($total_invoiced_currency);

    /**
     * get total_refunded_amount
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
     * total_refunded_currency
     *
     * @return string
     */
    public function getTotalRefundedCurrency();

    /**
     * Set total_refunded_currency
     *
     * @param string $total_refunded_currency
     * @return $this
     */
    public function setTotalRefundedCurrency($total_refunded_currency);

    /**
     * get total_revenue_amount
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
     * @param string $v
     * @return $this
     */
    public function setTotalRevenueCurrency($total_revenue_currency);
    
}
