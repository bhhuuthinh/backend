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
interface SalespaymenttypeInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@-*/

    /**
     * method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set method
     *
     * @param string $method
     * @return $this
     */
    public function setMethod($method);

    /**
     * method_label
     *
     * @return string
     */
    public function getMethodLabel();

    /**
     * Set method_label
     *
     * @param string $method_label
     * @return $this
     */
    public function setMethodLabel($method_label);


    /**
     * Period Label
     *
     * @return string
     */
    public function getTotalQtyInvoiced();

    /**
     * Set period label
     *
     * @param string $total_qty_invoiced
     * @return $this
     */
    public function setTotalQtyInvoiced($total_qty_invoiced);

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
     * total_income_amount
     *
     * @return string
     */
    public function getTotalIncomeAmount();

    /**
     * Set total_income_amount
     *
     * @param string $total_income_amount
     * @return $this
     */
    public function setTotalIncomeAmount($total_income_amount);

    /**
     * total_income_currency
     *
     * @return string
     */
    public function getTotalIncomeCurrency();

    /**
     * Set total_income_currency
     *
     * @param string $total_income_currency
     * @return $this
     */
    public function setTotalIncomeCurrency($total_income_currency);

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
     * total_income_currency
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

    /**
     * total_profit_amount
     *
     * @return string
     */
    public function getTotalProfitAmount();

    /**
     * Set total_profit_amount
     *
     * @param string $total_profit_amount
     * @return $this
     */
    public function setTotalProfitAmount($total_profit_amount);

    /**
     * total_profit_currency
     *
     * @return string
     */
    public function getTotalProfitCurrency();

    /**
     * Set total_profit_currency
     *
     * @param string $total_profit_currency
     * @return $this
     */
    public function setTotalProfitCurrency($total_profit_currency);

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
     * total_paid_amount
     *
     * @return string
     */
    public function getTotalPaidAmount();

    /**
     * Set total_paid_amount
     *
     * @param string $total_paid_amount
     * @return $this
     */
    public function setTotalPaidAmount($total_paid_amount);

    /**
     * total_paid_currency
     *
     * @return string
     */
    public function getTotalPaidCurrency();

    /**
     * Set total_paid_currency
     *
     * @param string $total_paid_currency
     * @return $this
     */
    public function setTotalPaidCurrency($total_paid_currency);

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
     * total_tax_amount_actual
     *
     * @return string
     */
    public function getTotalTaxAmountActual();

    /**
     * Set total_tax_amount_actual
     *
     * @param string $total_tax_amount_actual
     * @return $this
     */
    public function setTotalTaxAmountActual($total_tax_amount_actual);

    /**
     * total_tax_actual_currency
     *
     * @return string
     */
    public function getTotalTaxActualCurrency();

    /**
     * Set total_tax_actual_currency
     *
     * @param string $total_tax_actual_currency
     * @return $this
     */
    public function setTotalTaxActualCurrency($total_tax_actual_currency);

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
     * total_shipping_currency
     *
     * @return string
     */
    public function getTotalShippingCurrency();

    /**
     * Set total_shipping_currency
     *
     * @param string $total_shipping_currency
     * @return $this
     */
    public function setTotalShippingCurrency($total_shipping_currency);

    /**
     * total_shipping_amount_actual
     *
     * @return string
     */
    public function getTotalShippingAmountActual();

    /**
     * Set total_shipping_amount_actual
     *
     * @param string $total_shipping_amount_actual
     * @return $this
     */
    public function setTotalShippingAmountActual($total_shipping_amount_actual);

    /**
     * total_shipping_actual_currency
     *
     * @return string
     */
    public function getTotalShippingActualCurrency();

    /**
     * Set total_shipping_actual_currency
     *
     * @param string $total_shipping_actual_currency
     * @return $this
     */
    public function setTotalShippingActualCurrency($total_shipping_actual_currency);

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
     * total_discount_amount_actual
     *
     * @return string
     */
    public function getTotalDiscountAmountActual();

    /**
     * Set total_discount_amount_actual
     *
     * @param string $total_discount_amount_actual
     * @return $this
     */
    public function setTotalDiscountAmountActual($total_discount_amount_actual);

    /**
     * total_discount_actual_currency
     *
     * @return string
     */
    public function getTotalDiscountActualCurrency();

    /**
     * Set total_discount_actual_currency
     *
     * @param string $total_discount_actual_currency
     * @return $this
     */
    public function setTotalDiscountActualCurrency($total_discount_actual_currency);

    /**
     * total_canceled_amount
     *
     * @return string
     */
    public function getTotalCanceledAmount();

    /**
     * Set total_canceled_amount
     *
     * @param string $total_canceled_amount
     * @return $this
     */
    public function setTotalCanceledAmount($total_canceled_amount);

    /**
     * total_canceled_currency
     *
     * @return string
     */
    public function getTotalCanceledCurrency();

    /**
     * Set total_canceled_currency
     *
     * @param string $total_canceled_currency
     * @return $this
     */
    public function setTotalCanceledCurrency($total_canceled_currency);
    
}
