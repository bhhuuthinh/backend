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
interface AbandoneddetailedInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@-*/

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
     * email Label
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email label 
     *
     * @param string $total_qty_invoiced
     * @return $email
     */
    public function setEmail($email);

    /**
     * items_count
     *
     * @return string
     */
    public function getItemsCount();

    /**
     * Set items_count
     *
     * @param string $items_count
     * @return $this
     */
    public function setItemsCount($items_count);

    /**
     * items_qty
     *
     * @return string
     */
    public function getItemsQty();

    /**
     * Set items_qty
     *
     * @param string $items_qty
     * @return $this
     */
    public function setItemsQty($items_qty);

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
     * coupon_code
     *
     * @return string
     */
    public function getCouponCode();

    /**
     * Set coupon_code
     *
     * @param string $coupon_code
     * @return $this
     */
    public function setCouponCode($coupon_code);

    /**
     * v
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
     * updated_at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     *
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at);

    /**
     * remote_ip
     *
     * @return string
     */
    public function getRemoteIp();

    /**
     * Set remote_ip
     *
     * @param string $remote_ip
     * @return $this
     */
    public function setRemoteIp($remote_ip);
    
}
