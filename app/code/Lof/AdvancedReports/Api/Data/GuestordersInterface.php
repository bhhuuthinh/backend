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
interface GuestordersInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@-*/

    /**
     * increment_id
     *
     * @return string
     */
    public function getIncrementId();

    /**
     * Set increment_id
     *
     * @param string $increment_id
     * @return $this
     */
    public function setIncrementId($increment_id);


    /**
     * status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * status_label
     *
     * @return string
     */
    public function getStatusLabel();

    /**
     * Set status_label
     *
     * @param string $status_label
     * @return $this
     */
    public function setStatusLabel($status_label);

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
     * customer_email
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * Set customer_email
     *
     * @param string $customer_email
     * @return $this
     */
    public function setCustomerEmail($customer_email);

    /**
     * customer_firstname
     *
     * @return string
     */
    public function getCustomerFirstname();

    /**
     * Set customer_firstname
     *
     * @param string $customer_firstname
     * @return $this
     */
    public function setCustomerFirstname($customer_firstname);

    /**
     * customer_lastname
     *
     * @return string
     */
    public function getCustomerLastname();

    /**
     * Set customer_lastname
     *
     * @param string $customer_lastname
     * @return $this
     */
    public function setCustomerLastname($customer_lastname);

    /**
     * created_at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at);

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
     * total_qty_invoiced
     *
     * @return string
     */
    public function getTotalQtyInvoiced();

    /**
     * Set total_qty_invoiced
     *
     * @param string $total_qty_invoiced
     * @return $this
     */
    public function setTotalQtyInvoiced($total_qty_invoiced);

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
     * total_discount_amount_currency
     *
     * @return string
     */
    public function getTotalDiscountAmountCurrency();

    /**
     * Set total_discount_amount_currency
     *
     * @param string $total_discount_amount_currency
     * @return $this
     */
    public function setTotalDiscountAmountCurrency($total_discount_amount_currency);

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
     * total_shipping_amount_currency
     *
     * @return string
     */
    public function getTotalShippingAmountCurrency();

    /**
     * Set total_shipping_amount_currency
     *
     * @param string $total_shipping_amount_currency
     * @return $this
     */
    public function setTotalShippingAmountCurrency($total_shipping_amount_currency);

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
     * total_tax_amount_currency
     *
     * @return string
     */
    public function getTotalTaxAmountCurrency();

    /**
     * Set total_tax_amount_currency
     *
     * @param string $total_tax_amount_currency
     * @return $this
     */
    public function setTotalTaxAmountCurrency($total_tax_amount_currency);

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
     * total_cost_amount
     *
     * @return string
     */
    public function getTotalCostAmount();

    /**
     * Set total_cost_amount
     *
     * @param string $total_cost_amount
     * @return $this
     */
    public function setTotalCostAmount($total_cost_amount);

    /**
     * total_cost_currency
     *
     * @return string
     */
    public function getTotalCostCurrency();

    /**
     * Set total_cost_currency
     *
     * @param string $total_cost_currency
     * @return $this
     */
    public function setTotalCostCurrency($total_cost_currency);
    
}
