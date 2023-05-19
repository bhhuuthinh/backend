<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_AdvancedReports
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\AdvancedReports\Api\Data;

/**
 * @api
 */
interface AbandoneddataInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Lof\AdvancedReports\Api\Data\AbandonedInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Lof\AdvancedReports\Api\Data\AbandonedInterface $items
     * @return $this
     */
    public function setItems(array $items);
}