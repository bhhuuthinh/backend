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
interface StatisticsInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const PERIOD = 'period';

    const ORDERS_COUNT = 'orders_count';

    const TOTAL_ITEM_COUNT = 'total_item_count';

    const TOTAL_QTY_ORDERED = 'total_qty_ordered';

    const TOTAL_REVENUE_AMOUNT = 'total_revenue_amount';

    /**#@-*/

    /**
     * getTotalCurrentMonth
     *
     * @return string
     */
    public function getTotalCurrentMonth();

    /**
     * Set setTotalCurrentMonth
     *
     * @param string $total
     * @return $this
     */
    public function setTotalCurrentMonth($total);

    /**
     * getEarningCurrentMonth
     *
     * @return string
     */
    public function getEarningCurrentMonth();

    /**
     * Set setEarningCurrentMonth
     *
     * @param string $earning
     * @return $this
     */
    public function setEarningCurrentMonth($earning);

    /**
     * getTotalLatestMonth
     *
     * @return string
     */
    public function getTotalLatestMonth();

    /**
     * Set setTotalLatestMonth
     *
     * @param string $total
     * @return $this
     */
    public function setTotalLatestMonth($total);

    /**
     * getEarningLatestMonth
     *
     * @return string
     */
    public function getEarningLatestMonth();

    /**
     * Set setEarningLatestMonth
     *
     * @param string $earning
     * @return $this
     */
    public function setEarningLatestMonth($earning);

    /**
     * getTotalAll
     *
     * @return string
     */
    public function getTotalAll();

    /**
     * Set setTotalAll
     *
     * @param string $total
     * @return $this
     */
    public function setTotalAll($total);

    /**
     * getEarningAll
     *
     * @return string
     */
    public function getEarningAll();

    /**
     * Set setEarningAll
     *
     * @param string $earning
     * @return $this
     */
    public function setEarningAll($earning);

    /**
     * getBestSellers
     *
     * @return mixed
     */
    public function getBestSellers();

    /**
     * Set setBestSellers
     *
     * @param array $items
     * @return $this
     */
    public function setBestSellers($items);

    /**
     * getTopCountries
     *
     * @return mixed
     */
    public function getTopCountries();

    /**
     * Set setTopCountries
     *
     * @param array $items
     * @return $this
     */
    public function setTopCountries($items);

    /**
     * getTopPayments
     *
     * @return mixed
     */
    public function getTopPayments();

    /**
     * Set setTopPayments
     *
     * @param array $items
     * @return $this
     */
    public function setTopPayments($items);
    
}
