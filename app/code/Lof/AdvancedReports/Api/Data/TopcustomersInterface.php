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
interface TopcustomersInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{

    /**#@-*/

    /**
     * Customer Id
     *
     * @return string
     */
    public function getCustomerId();

    /**
     * Set customer_id
     *
     * @param string $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id);

     /**
     * customer_name
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * Set customer_name
     *
     * @param string $customer_name
     * @return $this
     */
    public function setCustomerName($customer_name);

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
     * profit_currency
     *
     * @return string
     */
    public function getProfitCurrency();

    /**
     * Set profit_currency
     *
     * @param string $profit_currency
     * @return $this
     */
    public function setProfitCurrency($profit_currency);
}
