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
namespace Lof\AdvancedReports\Block\Adminhtml\Grid\Column\Renderer; 

use Magento\Framework\Locale\Bundle\DataBundle;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

/**
 * Adminhtml grid item renderer date
 */
class Dateperiod extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Date
{

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolver; 
    protected $_helperData;
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Framework\Locale\ResolverInterface $localeResolver, 
        \Lof\AdvancedReports\Helper\Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $dateTimeFormatter, $data);
        $this->localeResolver = $localeResolver; 
        $this->_helperData = $helperData;
    }

    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            $dataBundle = new DataBundle();
            $resourceBundle = $dataBundle->get($this->localeResolver->getLocale());
            $formats = $resourceBundle['calendar']['gregorian']['availableFormats'];
            switch ($this->getColumn()->getPeriodType()) {
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

    public function render(\Magento\Framework\DataObject $row)
    {
        $show_link = $this->getColumn()->getShowLink();
        $data_filter = $this->getColumn()->getDataFilter();
        $filterData = $this->getColumn()->getFilterData();
        $filter_from = $filterData->getData('filter_from', null);
        $filter_to = $filterData->getData('filter_to', null);
        $data = $org_data = $row->getData($this->getColumn()->getIndex()); 
        $format = $this->_getFormat();
        $data_filter_from = $data_filter_to = ""; 
        try {
            switch ($this->getColumn()->getPeriodType()) {
                case 'week' : 
                    $date_range = $this->_helperData->getWeekRange( $data ); 
                    if(count($date_range) == 2) {   
                        if(strtotime($date_range[0]) < strtotime($filter_from)) {
                            $date_range[0] = date("Y-m-d", strtotime($filter_from));
                        }

                        if(strtotime($date_range[1]) > strtotime($filter_to)) {
                            $date_range[1] = date("Y-m-d", strtotime($filter_to));
                        } 
                        if ($this->getColumn()->getGmtoffset() || $this->getColumn()->getTimezone()) { 
                             $start_date = $this->_localeDate->date(new \DateTime($date_range[0]));
                             $end_date   = $this->_localeDate->date(new \DateTime($date_range[1]));
                        } else {
                             $start_date = $this->_localeDate->date(new \DateTime($date_range[0]), null, false);
                             $end_date = $this->_localeDate->date(new \DateTime($date_range[1]), null, false);
                        } 
                         $start_date = $this->dateTimeFormatter->formatObject($start_date, $format, $this->localeResolver->getLocale());
                         $end_date = $this->dateTimeFormatter->formatObject($end_date, $format, $this->localeResolver->getLocale()); 


                    $data = $start_date ." - ".$end_date;

                    $data_filter_from = date("m/d/Y", strtotime($start_date));
                    $data_filter_to = date("m/d/Y", strtotime($end_date));  
                    } 
                    break;
                case 'day' :
                
                    $data_filter_from = $data_filter_to = date("m/d/Y", strtotime($data));
                    if ($this->getColumn()->getGmtoffset() || $this->getColumn()->getTimezone()) { 
                         $date = $this->_localeDate->date(new \DateTime($data));
                    } else {
                         $date = $this->_localeDate->date(new \DateTime($data), null, false);
                    } 
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
            if ($this->getColumn()->getGmtoffset() || $this->getColumn()->getTimezone()) { 
                 $date = $this->_localeDate->date(new \DateTime($data));
            } else {
                 $date = $this->_localeDate->date(new \DateTime($data), null, false);
            } 
            $data = $this->dateTimeFormatter->formatObject($date, $format, $this->localeResolver->getLocale());
        }

        if($data) {
            if($show_link && $data_filter ){
                $date_range = isset($data_filter['date_range'])?$data_filter['date_range']:array();
                $route = isset($data_filter['route'])?$data_filter['route']:"";
                if(count($date_range) >= 2 && $route) {

                    if(!$data_filter_from && !$data_filter_to) {
                        $cur_month = date("m");
                        $cur_year = date("Y");
                        $data_filter_from = $cur_month."/01/".$cur_year;
                        $data_filter_to = date("m/d/Y");
                    }
                    $filterData = array();
                    $filterData[] = 'filter_from='.$data_filter_from;
                    $filterData[] = 'filter_to='.$data_filter_to;
                    $filterData[] = 'report_field=main_table.created_at';

                    
                    $filter = implode("&",$filterData);
                    $filter = base64_encode($filter);

                    $is_export = $this->getColumn()->getIsExport();
                    if(!$is_export) {
                        $data = sprintf('<a href="%s" title="%s">%s</a>',
                                $this->getUrl($route, array('loffilter' => $filter)),
                                __('Show Detail'), $data
                            );
                    }
                } 
            }
            return $data;
        }

        return $this->getColumn()->getDefault();
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
