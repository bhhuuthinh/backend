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
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */ 
namespace Lof\AdvancedReports\Helper\GoogleChart;
use Lof\AdvancedReports\Helper\Data as HelperData;
class  AbstractChart extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
	protected $_helperData;
	protected $_filterProvider;
	protected $storeManager;
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        HelperData $helperData
        ) { 
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->_helperData = $helperData;
    }
	protected $_api_link = "https://www.gstatic.com/charts/loader.js";

	public function getGoogleApiScript($api_key = "", $secret = "") {
		return $this->_api_link;
	}

	public function buildGoogleApiScript($api_key = "", $secret = "") {
		$api_link = $this->getGoogleApiScript($api_key, $secret);
		return '<script type="text/javascript" src="'.$api_link.'"></script>';
	}
	public function initGoogleChart ($settings = array(), $call_back_func = "drawChart") {
		$html = '';
		$html .= "
			google.charts.load('current', {'packages':['corechart']});
			google.charts.setOnLoadCallback(".$call_back_func.");
		";
		return $html;
	} 
	public function buildChart($data = null, $settings = array()) {return "";} 
}