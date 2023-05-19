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
interface SalescategoryInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@-*/

    /**
     * category_id
     *
     * @return string
     */
    public function getCategoryId();

    /**
     * Set category_id
     *
     * @param string $category_id
     * @return $this
     */
    public function setCategoryId($category_id);

    /**
     * category_name
     *
     * @return string
     */
    public function getCategoryName();

    /**
     * Set category_name
     *
     * @param string $category_name
     * @return $this
     */
    public function setCategoryName($category_name);


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
     * total_income_amount
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
     * total_income_currency
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
