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
interface CustomercityInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
     * city
     *
     * @return string
     */
    public function getCity();

    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city);

    /**
     * new_accounts_count
     *
     * @return string
     */
    public function getNewAccountsCount();

    /**
     * Set new_accounts_count
     *
     * @param string $new_accounts_count
     * @return $this
     */
    public function setNewAccountsCount($new_accounts_count);

    /**
     * new_accounts_orders_count
     *
     * @return string
     */
    public function getNewAccountsOrdersCount();

    /**
     * Set new_accounts_orders_count
     *
     * @param string $new_accounts_orders_count
     * @return $this
     */
    public function setNewAccountsOrdersCount($new_accounts_orders_count);

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
     * @param string $subtotal_currency
     * @return $this
     */
    public function setTotalSubtotalCurrency($total_subtotal_currency);

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
     * total_tax_currency
     *
     * @return string
     */
    public function getTotalTaxCurrency();

    /**
     * Set total_tax_currency
     *
     * @param string $total_tax_currency
     * @return $this
     */
    public function setTotalTaxCurrency($total_tax_currency);

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


}
