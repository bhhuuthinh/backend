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
interface SettingInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const SETTINGS = 'settings';


    /**#@-*/

    /**
     * getSettings
     *
     * @return string
     */
    public function getSettings();

    
    /**
     * Set setSettings
     *
     * @param array $settings
     * @return $this
     */
    public function setSettings($settings);

}
