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

namespace Lof\AdvancedReports\Model;
use Lof\AdvancedReports\Api\SettingInterface;
use Lof\AdvancedReports\Model\AbstractReport;
use Magento\Framework\Api\SortOrder;
 
class SettingRepository extends EarningRepository implements SettingInterface
{
  
    /**
     * @return \Lof\AdvancedReports\Api\Data\ReportdataInterface
     */
    public function getSetting() {
        //init array
        $arr_config = $this->_helperData->getConfig("mobile_settings");
        if($arr_config) {
           if(isset($arr_config['allow_reports']) && $arr_config['allow_reports']) {
                $arr_config['allow_reports'] = explode(",", $arr_config['allow_reports']);
           }
           $tmp_config = [];
           foreach($arr_config as $key => $val) {
                $tmp = array();
                $tmp['config'] = $key;
                $tmp['value'] = $val;

                $tmp_config[] = $tmp;
           }
           $arr_config = $tmp_config;
        } else {
            $arr_config = [];
        }
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setItems($arr_config);
        $searchResult->setTotalCount(1);

        return $searchResult;
    }
}