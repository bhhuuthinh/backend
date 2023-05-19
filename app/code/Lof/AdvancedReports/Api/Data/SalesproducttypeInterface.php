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
interface SalesproducttypeInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@-*/

    /**
     * Period
     *
     * @return string
     */
    public function getProductType();

    /**
     * Set period
     *
     * @param string $period
     * @return $this
     */
    public function setProductType($prduct_type);


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
    
}
