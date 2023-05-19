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
interface ItemdetailedInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
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
     * sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * real_qty_ordered
     *
     * @return string
     */
    public function getRealQtyOrdered();

    /**
     * Set real_qty_ordered
     *
     * @param string $real_qty_ordered
     * @return $this
     */
    public function setRealQtyOrdered($real_qty_ordered);

    /**
     * qty_invoiced
     *
     * @return string
     */
    public function getQtyInvoiced();

    /**
     * Set qty_invoiced
     *
     * @param string $qty_invoiced
     * @return $this
     */
    public function setQtyInvoiced($qty_invoiced);

    /**
     * real_qty_shipped
     *
     * @return string
     */
    public function getRealQtyShipped();

    /**
     * Set real_qty_shipped
     *
     * @param string $real_qty_shipped
     * @return $this
     */
    public function setRealQtyShipped($real_qty_shipped);

    /**
     * real_qty_refunded
     *
     * @return string
     */
    public function getRealQtyRefunded();

    /**
     * Set real_qty_refunded
     *
     * @param string $real_qty_refunded
     * @return $this
     */
    public function setRealQtyRefunded($real_qty_refunded);

    /**
     * price
     *
     * @return string
     */
    public function getPrice();

    /**
     * Set price
     *
     * @param string $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * price_currency
     *
     * @return string
     */
    public function getPriceCurrency();

    /**
     * Set price_currency
     *
     * @param string $price_currency
     * @return $this
     */
    public function setPriceCurrency($price_currency);

    /**
     * base_price_currency
     *
     * @return string
     */
    public function getBasePrice();

    /**
     * Set base_price_currency
     *
     * @param string $base_price_currency
     * @return $this
     */
    public function setBasPrice($base_price);

    /**
     * base_price_currency
     *
     * @return string
     */
    public function getBasePriceCurrency();

    /**
     * Set base_price_currency
     *
     * @param string $base_price_currency
     * @return $this
     */
    public function setBasPriceCurrency($base_price_currency);

    /**
     * subtotal
     *
     * @return string
     */
    public function getSubtotal();

    /**
     * Set subtotal
     *
     * @param string $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal);

    /**
     * subtotal_currency
     *
     * @return string
     */
    public function getSubtotalCurrency();

    /**
     * Set subtotal_currency
     *
     * @param string $subtotal_currency
     * @return $this
     */
    public function setSubtotalCurrency($subtotal_currency);

    /**
     * discount_amount
     *
     * @return string
     */
    public function getDiscountAmount();

    /**
     * Set discount_amount
     *
     * @param string $discount_amount
     * @return $this
     */
    public function setDiscountAmount($discount_amount);

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
     * tax_amount
     *
     * @return string
     */
    public function getTaxAmount();

    /**
     * Set tax_amount
     *
     * @param string $tax_amount
     * @return $this
     */
    public function setTaxAmount($tax_amount);

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
     * row_total
     *
     * @return string
     */
    public function getRowTotal();

    /**
     * Set row_total
     *
     * @param string $row_total
     * @return $this
     */
    public function setRowTotal($row_total);

     /**
     * row_total_currency
     *
     * @return string
     */
    public function getRowTotalCurrency();

    /**
     * Set row_total_currency
     *
     * @param string $row_total_currency
     * @return $this
     */
    public function setRowTotalCurrency($row_total_currency);

    /**
     * row_total_incl_tax
     *
     * @return string
     */
    public function getRowTotalInclTax();

    /**
     * Set row_total_incl_tax
     *
     * @param string $row_total_incl_tax
     * @return $this
     */
    public function setRowTotalInclTax($row_total_incl_tax);

    /**
     * row_total_incl_tax_currency
     *
     * @return string
     */
    public function getRowTotalInclTaxCurrency();

    /**
     * Set row_total_incl_tax_currency
     *
     * @param string $row_total_incl_tax_currency
     * @return $this
     */
    public function setRowTotalInclTaxCurrency($row_total_incl_tax_currency);

    /**
     * row_invoiced
     *
     * @return string
     */
    public function getRowInvoiced();

    /**
     * Set row_invoiced
     *
     * @param string $row_invoiced
     * @return $this
     */
    public function setRowInvoiced($row_invoiced);

    /**
     * row_invoiced_currency
     *
     * @return string
     */
    public function getRowInvoicedCurrency();

    /**
     * Set row_invoiced_currency
     *
     * @param string $row_invoiced_currency
     * @return $this
     */
    public function setRowInvoicedCurrency($row_invoiced_currency);

    /**
     * tax_invoiced
     *
     * @return string
     */
    public function getTaxInvoiced();

    /**
     * Set tax_invoiced
     *
     * @param string $tax_invoiced
     * @return $this
     */
    public function setTaxInvoiced($tax_invoiced);

    /**
     * tax_invoiced
     *
     * @return string
     */
    public function getTaxInvoicedCurrency();

    /**
     * Set tax_invoiced_currency
     *
     * @param string $tax_invoiced_currency
     * @return $this
     */
    public function setTaxInvoicedCurrency($tax_invoiced_currency);

    /**
     * row_invoiced_incl_tax
     *
     * @return string
     */
    public function getRowInvoicedInclTax();

    /**
     * Set row_invoiced_incl_tax
     *
     * @param string $row_invoiced_incl_tax
     * @return $this
     */
    public function setRowInvoicedInclTax($row_invoiced_incl_tax);

    /**
     * row_invoiced_incl_tax_currency
     *
     * @return string
     */
    public function getRowInvoicedInclTaxCurrency();

    /**
     * Set row_invoiced_incl_tax_currency
     *
     * @param string $row_invoiced_incl_tax_currency
     * @return $this
     */
    public function setRowInvoicedInclTaxCurrency($row_invoiced_incl_tax_currency);

    /**
     * amount_refunded
     *
     * @return string
     */
    public function getAmountRefunded();

    /**
     * Set amount_refunded
     *
     * @param string $amount_refunded
     * @return $this
     */
    public function setAmountRefunded($amount_refunded);

     /**
     * amount_refunded_currency
     *
     * @return string
     */
    public function getAmountRefundedCurrency();

    /**
     * Set amount_refunded_currency
     *
     * @param string $amount_refunded_currency
     * @return $this
     */
    public function setAmountRefundedCurrency($amount_refunded_currency);

    /**
     * real_tax_refunded
     *
     * @return string
     */
    public function getRealTaxRefunded();

    /**
     * Set real_tax_refunded
     *
     * @param string $real_tax_refunded
     * @return $this
     */
    public function setRealTaxRefunded($real_tax_refunded);

    /**
     * real_tax_refunded_currency
     *
     * @return string
     */
    public function getRealTaxRefundedCurrency();

    /**
     * Set real_tax_refunded_currency
     *
     * @param string $real_tax_refunded_currency
     * @return $this
     */
    public function setRealTaxRefundedCurrency($real_tax_refunded_currency);

    /**
     * row_refunded_incl_tax
     *
     * @return string
     */
    public function getRowRefundedInclTax();

    /**
     * Set row_refunded_incl_tax
     *
     * @param string $row_refunded_incl_tax
     * @return $this
     */
    public function setRowRefundedInclTax($row_refunded_incl_tax);

    /**
     * row_refunded_incl_tax_currency
     *
     * @return string
     */
    public function getRowRefundedInclTaxCurrency();

    /**
     * Set row_refunded_incl_tax_currency
     *
     * @param string $row_refunded_incl_tax_currency
     * @return $this
     */
    public function setRowRefundedInclTaxCurrency($row_refunded_incl_tax_currency);

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

    /**
     * total_revenue_amount_excl_tax
     *
     * @return string
     */
    public function getTotalRevenueAmountExclTax();

    /**
     * Set total_revenue_amount_excl_tax
     *
     * @param string $total_revenue_amount_excl_tax
     * @return $this
     */
    public function setTotalRevenueAmountExclTax($total_revenue_amount_excl_tax);

    /**
     * total_revenue_amount_excl_tax_currency
     *
     * @return string
     */
    public function getTotalRevenueAmountExclTaxCurrency();

    /**
     * Set total_revenue_amount_excl_tax_currency
     *
     * @param string $total_revenue_amount_excl_tax_currency
     * @return $this
     */
    public function setTotalRevenueAmountExclTaxCurrency($total_revenue_amount_excl_tax_currency);

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
     * total_margin
     *
     * @return string
     */
    public function getTotalMargin();

    /**
     * Set total_margin
     *
     * @param string $total_margin
     * @return $this
     */
    public function setTotalMargin($total_margin);
    
}
