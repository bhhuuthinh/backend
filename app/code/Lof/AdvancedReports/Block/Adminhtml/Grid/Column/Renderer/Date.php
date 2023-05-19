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
class Date extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Date
{

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolver;

    /**
     * @var \Lof\AdvancedReports\Helper\Data
     */
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

    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $date = date("d",strtotime($row->getCreatedAt()));
        $filter_link = $this->getFilterLink($date);
        if ($data = $row->getCreatedAt()) {
            switch ($this->getColumn()->getPeriodType()) {
                case 'month':
                    $data = $data . '-01';
                    $filter_link = $this->getFilterLink(date("m",strtotime($data)));
                    break;
                case 'year':
                    $data = $data . '-01-01';
                    $filter_link = $this->getFilterLink(date("Y",strtotime($data)));
                    break;
                case 'hour':
                    $start_time = $this->_lz((int)($row->getData($this->getColumn()->getIndex()))).":00";
                    $end_time = $this->_lz((int)($row->getData($this->getColumn()->getIndex()))+1).":00";
                    $data = $start_time." - ".$end_time;
                    break;
            }
            if($this->getColumn()->getPeriodType() === "hour") {
                return $data;
            }
            $format = $this->_getFormat();
            if ($this->getColumn()->getGmtoffset() || $this->getColumn()->getTimezone()) {
                 $date = $this->_localeDate->date(new \DateTime($data));
            } else {
                 $date = $this->_localeDate->date(new \DateTime($data), null, false);
            }
            $data = $this->dateTimeFormatter->formatObject($date, $format, $this->localeResolver->getLocale());
            if($data) {
                if($this->getColumn()->getIsExport() == 1){
                    return $data;
                }
            if($filter_link) {
                return '<a href="'.$filter_link.'" title="'.__("Click To View Details").'" onclick ="filterFormSubmit()"><strong>'.$data.'</strong></a>';
            } else {
                return $data;
            }
        }
        }

        return $this->getColumn()->getDefault();
    }

    public function getFilterLink ($row_value = null) {
        $filterData = $this->getColumn()->getFilterData();
        $filter_year = $filterData->getData('filter_year', null);
        $filter_month = $filterData->getData('filter_month', null);
        $filter_day = $filterData->getData('filter_day', null);
        $url = false;
        $filter = array();
        if(!$filter_year) {
            $filter[] = 'filter_year='.$row_value;
        } elseif($filter_year && !$filter_month) {
            $filter[] = 'filter_year='.$filter_year;
            $filter[] = 'filter_month='.$row_value;
        } elseif($filter_year && $filter_month && !$filter_day) {
            $filter[] = 'filter_year='.$filter_year;
            $filter[] = 'filter_month='.$filter_month;
            $filter[] = 'filter_day='.$row_value;
        }
        $showorderStatuses = $filterData->getData('show_order_statuses', null);
        $filter[] = 'show_order_statuses='.$showorderStatuses;
        $orderStatuses = $filterData->getData('order_statuses', null);
            if(is_array($orderStatuses)){
                $orderStatuses = implode(',',$orderStatuses);
            }
        $filter[] = 'order_statuses='.$orderStatuses;
        if($filter) {
            $filter = implode("&",$filter);
            $filter = base64_encode($filter);
            $url= $this->getUrl('*/*/earning', array('loffilter'=>$filter));
        }
        return $url;
    }
     protected function _lz($num)
    {
        return (strlen($num) < 2) ? "0{$num}" : $num;
    }
}
