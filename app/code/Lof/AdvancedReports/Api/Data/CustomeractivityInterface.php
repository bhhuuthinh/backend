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
interface CustomeractivityInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PERIOD = 'period';

    const PERIOD_LABEL= 'period_label';

    const ORDERS_COUNT = 'orders_count';

    const NEW_ACCOUNTS_COUNT = 'new_accounts_count';

    const REVIEWS_COUNT = 'reviews_count';
    

    

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
     * Period Label
     *
     * @return string
     */
    public function getPeriodLabel();

    /**
     * Set period label
     *
     * @param string $period_label
     * @return $this
     */
    public function setPeriodLabel($period_label);

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
     * new_accounts_count
     *
     * @return string
     */
    public function getNewAccountsCount();

    /**
     * Set new_accounts_count
     *
     * @param string $new_accounts_count
     * @return $this
     */
    public function setNewAccountsCount($new_accounts_count);

    /**
     * reviews_count
     *
     * @return string
     */
    public function getReviewsCount();

    /**
     * Set reviews_count
     *
     * @param string $reviews_count
     * @return $this
     */
    public function setReviewsCount($reviews_count);


}
