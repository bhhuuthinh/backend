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
interface ProductsreportInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**#@-*/

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
     * v
     *
     * @return string
     */
    public function getProductType();

    /**
     * Set product_type
     *
     * @param string $product_type
     * @return $this
     */
    public function setProductType($product_type);

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
     * total_qty_refunded
     *
     * @return string
     */
    public function getTotalQtyRefunded();

    /**
     * Set total_qty_refunded
     *
     * @param string $total_qty_refunded
     * @return $this
     */
    public function setTotalQtyRefunded($total_qty_refunded);

    
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
     * avg_price
     *
     * @return string
     */
    public function getAvgPrice();

    /**
     * Set avg_price
     *
     * @param string $avg_price
     * @return $this
     */
    public function setAvgPrice($avg_price);

     /**
     * avg_price_currency
     *
     * @return string
     */
    public function getAvgPriceCurrency();

    /**
     * Set avg_price_currency
     *
     * @param string $avg_price_currency
     * @return $this
     */
    public function setAvgPriceCurrency($avg_price_currency);

    /**
     * avg_qty
     *
     * @return string
     */
    public function getAvgQty();

    /**
     * Set avg_qty
     *
     * @param string $avg_qty
     * @return $this
     */
    public function setAvgQty($avg_qty);

    /**
     * avg_revenue
     *
     * @return string
     */
    public function getAvgRevenue();

    /**
     * Set avg_revenue
     *
     * @param string $avg_revenue
     * @return $this
     */
    public function setAvgRevenue($avg_revenue);

    /**
     * avg_revenue_currency
     *
     * @return string
     */
    public function getAvgRevenueCurrency();

    /**
     * Set avg_revenue_currency
     *
     * @param string $avg_revenue_currency
     * @return $this
     */
    public function setAvgRevenueCurrency($avg_revenue_currency);

    /**
     * avg_discount_amount
     *
     * @return string
     */
    public function getAvgDiscountAmount();

    /**
     * Set avg_discount_amount
     *
     * @param string $avg_discount_amount
     * @return $this
     */
    public function setAvgDiscountAmount($avg_discount_amount);

    /**
     * avg_discount_currency
     *
     * @return string
     */
    public function getAvgDiscountCurrency();

    /**
     * Set avg_discount_currency
     *
     * @param string $avg_discount_currency
     * @return $this
     */
    public function setAvgDiscountCurrency($avg_discount_currency);

    /**
     * avg_tax_amount
     *
     * @return string
     */
    public function getAvgTaxAmount();

    /**
     * Set avg_tax_amount
     *
     * @param string $avg_tax_amount
     * @return $this
     */
    public function setAvgTaxAmount($avg_tax_amount);

    /**
     * avg_tax_currency
     *
     * @return string
     */
    public function getAvgTaxCurrency();

    /**
     * Set avg_tax_currency
     *
     * @param string $avg_tax_currency
     * @return $this
     */
    public function setAvgTaxCurrency($avg_tax_currency);

    /**
     * avg_refunded_amount
     *
     * @return string
     */
    public function getAvgRefundedAmount();

    /**
     * Set avg_refunded_amount
     *
     * @param string $avg_refunded_amount
     * @return $this
     */
    public function setAvgRefundedAmount($avg_refunded_amount);

    /**
     * avg_refunded_currency
     *
     * @return string
     */
    public function getAvgRefundedCurrency();

    /**
     * Set avg_refunded_currency
     *
     * @param string $avg_refunded_currency
     * @return $this
     */
    public function setAvgRefundedCurrency($avg_refunded_currency);


}
