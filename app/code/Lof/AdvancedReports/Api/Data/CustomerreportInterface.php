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
interface CustomerreportInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const CUSTOMER_ID = 'customer_id';

    const CUSTOMER_FIRSTNAME = 'customer_firstname';

    const CUSTOMER_LASTNAME = 'customer_lastname';

    const CUSTOMER_EMAIL = 'customer_email';

    const CITY = 'city';

    const POSTCODE = 'postcode';

    const ORDERS_COUNT = 'orders_count';

    const TOTAL_SUBTOTAL_AMOUNT = 'total_subtotal_amount';

    const SUBTOTAL_CURRENCY = 'subtotal_currency';

    const TOTAL_GRANDTOTAL_AMOUNT = 'total_grandtotal_amount';

    const GRANDTOTAL_CURRENCY = 'grandtotal_currency';

    const TOTAL_PROFIT_AMOUNT = 'total_profit_amount';

    const PROFIT_CURRENCY = 'profit_currency';

    const TOTAL_SHIPPING_AMOUNT = 'total_shipping_amount';

    const SHIPPING_CURRENCY = 'shipping_currency';

    const TOTAL_TAX_AMOUNT = 'total_tax_amount';

    const TAX_CURRENCY = 'tax_currency';

    const TOTAL_DISCOUNT_AMOUNT = 'total_discount_amount';

    const DISCOUNT_CURRENCY = 'discount_currency';

    const TOTAL_REFUNDED_AMOUNT = 'total_refunded_amount';

    const REFUNDED_CURRENCY = 'refunded_currency';
    

    

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
     * Set customer_firscustomer_lastnametname
     *
     * @param string $customer_lastname
     * @return $this
     */
    public function setCustomerLastname($customer_lastname);

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
     * postcode
     *
     * @return string
     */
    public function getPostcode();

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode);

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
     * grandtotal_currency
     *
     * @return string
     */
    public function getGrandtotalCurrency();

    /**
     * Set grandtotal_currency
     *
     * @param string $grandtotal_currency
     * @return $this
     */
    public function setGrandtotalCurrency($grandtotal_currency);


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
     * shipping_currency
     *
     * @return string
     */
    public function getShippingCurrency();

    /**
     * Set shipping_currency
     *
     * @param string $shipping_currency
     * @return $this
     */
    public function setShippingCurrency($shipping_currency);

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
     * refunded_currency
     *
     * @return string
     */
    public function getRefundedCurrency();

    /**
     * Set refunded_currency
     *
     * @param string $refunded_currency
     * @return $this
     */
    public function setRefundedCurrency($refunded_currency);


}
