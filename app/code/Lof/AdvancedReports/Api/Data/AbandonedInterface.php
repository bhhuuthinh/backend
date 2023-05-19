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
interface AbandonedInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
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
     * period_label
     *
     * @return string
     */
    public function getPeriodLabel();

    /**
     * Set period_label
     *
     * @param string $period_label
     * @return $this
     */
    public function setPeriodLabel($period_label);

    /**
     * total_cart
     *
     * @return string
     */
    public function getTotalCart();

    /**
     * Set total_cart
     *
     * @param string $total_cart
     * @return $this
     */
    public function setTotalCart($total_cart);

    /**
     * total_completed_cart
     *
     * @return string
     */
    public function getTotalCompletedCart();

    /**
     * Set total_completed_cart
     *
     * @param string $total_completed_cart
     * @return $this
     */
    public function setTotalCompletedCart($total_completed_cart);

    /**
     * total_abandoned_cart
     *
     * @return string
     */
    public function getTotalAbandonedCart();

    /**
     * Set total_abandoned_cart
     *
     * @param string $total_abandoned_cart
     * @return $this
     */
    public function setTotalAbandonedCart($total_abandoned_cart);

    /**
     * abandoned_cart_total_amount
     *
     * @return string
     */
    public function getAbandonedCartTotalAmount();

    /**
     * Set abandoned_cart_total_amount
     *
     * @param string $abandoned_cart_total_amount
     * @return $this
     */
    public function setAbandonedCartTotalAmount($abandoned_cart_total_amount);

    /**
     * abandoned_cart_total_currency
     *
     * @return string
     */
    public function getAbandonedCartTotalCurrency();

    /**
     * Set abandoned_cart_total_currency
     *
     * @param string $abandoned_cart_total_currency
     * @return $this
     */
    public function setAbandonedCartTotalCurrency($abandoned_cart_total_currency);

    /**
     * abandoned_rate
     *
     * @return string
     */
    public function getAbandonedRate();

    /**
     * Set abandoned_rate
     *
     * @param string $abandoned_rate
     * @return $this
     */
    public function setAbandonedRate($abandoned_rate);
    
}
