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

namespace Lof\AdvancedReports\Helper\Api;
use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
class Datefield extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolver; 
    protected $_helperData;
    protected $_period_type;
    protected $_localeDate;
    protected $dateTimeFormatter;
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Framework\Locale\ResolverInterface $localeResolver, 
        \Lof\AdvancedReports\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        array $data = []
    ) {
        parent::__construct($context);
        $this->localeResolver = $localeResolver; 
        $this->_helperData = $helperData;
        $this->_localeDate = $localeDate;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function getPeriodType() {
    	return $this->_period_type;
    }
    public function setPeriodType($period_type = "month") {
    	$this->_period_type = $period_type;
    	return $this;
    }
    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat($format = "")
    {
        if (!$format) {
            $dataBundle = new DataBundle();
            $resourceBundle = $dataBundle->get($this->localeResolver->getLocale());
            $formats = $resourceBundle['calendar']['gregorian']['availableFormats'];
            switch ($this->getPeriodType()) {
                case 'month':
                    $format = $formats['yM'];
                    break;
                case 'year':
                    $format = $formats['y'];
                    break;   
                default:
                    $format = $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
                    break;
            }
        }
        return $format;
    }

    /**
     * Renders grid column
     *
     * @param mixed $row
     * @return string
     */
    public function renderPeriodField($date_data, $period_type = "month", $filter_year = "", $filter_month = "", $filter_day = "")
    {   
    	$this->setPeriodType($period_type);
        if ($data = $date_data) {
            switch ($period_type) {
                case 'year':
                    $data = $data . '-01-01';
                    break;
                case 'month':
                    $data = $filter_year.'-'.$data . '-01';
                    break;
                case 'day':
                    $filter_year = $filter_year?$filter_year:day("Y");
                    $filter_month = $filter_month?$filter_month:day("m");
                    $data = $filter_year.'-'.$filter_month. '-'.$data;
                    break;
                case 'hour':
                    $start_time = $this->_lz((int)($data)).":00";
                    $end_time = $this->_lz((int)($data)+1).":00";
                    $data = $start_time." - ".$end_time;  
                    break;
            } 
            if($period_type === "hour") {
                return $data; 
            }
            $format = $this->_getFormat(); 
            $date_value = $this->_localeDate->date(new \DateTime($data), null, false);
            $data = $this->dateTimeFormatter->formatObject($date_value, $format, $this->localeResolver->getLocale());
            if($data) { 
                return $data;
        	}
        }
        return $date_data;
    }
 
    public function renderDateperiod($date_data, $period_type = "month", $filterData = null)
    {
        $this->setPeriodType($period_type);
        $data = $org_data = $date_data;
        $filter_from = $filterData->getData('filter_from', null);
        $filter_to = $filterData->getData('filter_to', null);
        $format = $this->_getFormat();
        $data_filter_from = $data_filter_to = ""; 
        try {
            switch ($period_type) {
                case 'week' : 
                    $date_range = $this->_helperData->getWeekRange( $data ); 
                    if(count($date_range) == 2) {   
                        if(strtotime($date_range[0]) < strtotime($filter_from)) {
                            $date_range[0] = date("Y-m-d", strtotime($filter_from));
                        }

                        if(strtotime($date_range[1]) > strtotime($filter_to)) {
                            $date_range[1] = date("Y-m-d", strtotime($filter_to));
                        } 
                        $start_date = $this->_localeDate->date(new \DateTime($date_range[0]), null, false);
                        $end_date = $this->_localeDate->date(new \DateTime($date_range[1]), null, false);
                        
                        $start_date = $this->dateTimeFormatter->formatObject($start_date, $format, $this->localeResolver->getLocale());
                        $end_date = $this->dateTimeFormatter->formatObject($end_date, $format, $this->localeResolver->getLocale()); 


                    $data = $start_date ." - ".$end_date;

                    $data_filter_from = date("m/d/Y", strtotime($start_date));
                    $data_filter_to = date("m/d/Y", strtotime($end_date));  
                    } 
                    break;
                case 'day' :
                
                    $data_filter_from = $data_filter_to = date("m/d/Y", strtotime($data));
                    $date = $this->_localeDate->date(new \DateTime($data), null, false);
                    $data = $this->dateTimeFormatter->formatObject($date, $format, $this->localeResolver->getLocale()); 
                case 'quarter' :
                    $tmp = explode("/", $data);
                    if(count($tmp) >1) {
                        $data = "Q".$tmp[0].", ".$tmp[1];
                        $months = $this->_getMonthFromQuarter($tmp[0]);
                        $data_filter_from = $months['from']."/01/".$tmp[1];
                        $data_filter_to = date("m/d/Y", mktime(0, 0, 0, (int)$months['to']+1, 0, $tmp[1]));
                    } 
                break; 
                case 'weekday':
                    $dates = array(
                        6 =>  __("Sunday"),
                        0 =>  __("Monday"),
                        1 =>  __("Tuesday"),
                        2 =>  __("Wednesday"),
                        3 =>  __("Thursday"),
                        4 =>  __("Friday"),
                        5 =>  __("Saturday"),
                        );
                    $data = isset($dates[$data])?$dates[$data]:$data;

                    $data_filter_from = $filter_from;
                    $data_filter_to = $filter_to;
                break;
                case 'year' :
                    $data_filter_from = "01/01/".$data;
                    $data_filter_to = date("m/d/Y", mktime(0, 0, 0, 13, 0, $data));
                    break;
                default:
                    break;
            }
        }
        catch (Exception $e) {
            $date = $this->_localeDate->date(new \DateTime($data), null, false);
            $data = $this->dateTimeFormatter->formatObject($date, $format, $this->localeResolver->getLocale());
        }

        if($data) {
            return $data;
        }

        return $date_data;
    }

    protected function _lz($num)
    {
        return (strlen($num) < 2) ? "0{$num}" : $num;
    } 
    protected function _getWeekDay($day,$month,$year){
        return date("l",strtotime($year.'-'.$month.'-'.$day));
    }

    protected function _getMonthFromQuarter($quarter) {
        $month = array();
        switch ($quarter) {
            case '1':
                $month = array("from"=>1, "to"=>3);
                break;
            case '2':
                $month = array("from"=>3, "to"=>6);
                break;
            case '3':
                $month = array("from"=>6, "to"=>9);
                break;
            case '4':
                $month = array("from"=>9, "to"=>12);
                break;
        }
        return $month;
    }
}
