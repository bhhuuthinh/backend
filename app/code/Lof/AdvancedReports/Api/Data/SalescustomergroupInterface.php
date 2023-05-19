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
interface SalescustomergroupInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const GROUP_NAME = 'group_name';

    const TOTAL_QTY_INVOICED = 'total_qty_invoiced';

    const TOTAL_INCOME_AMOUNT = 'total_income_amount';

    const TOTAL_INCOME_AMOUNT_CURRENCY = 'total_income_currency';

    /**#@-*/

    /**
     * group_name
     *
     * @return string
     */
    public function getGroupName();

    /**
     * Set group_name
     *
     * @param string $group_name
     * @return $this
     */
    public function setGroupName($group_name);


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
