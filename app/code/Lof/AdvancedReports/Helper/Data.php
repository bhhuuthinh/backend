<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_AdvancedReports
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.Landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\AdvancedReports\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
    protected $storeManager;
    protected $_request;
    protected $_objectManager;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface  $objectManager
     * @param \Magento\Framework\App\Request\Http        $request
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    )
    {
        parent::__construct( $context );
        $this->storeManager    = $storeManager;
        $this->_request        = $request;
        $this->_filterProvider = $filterProvider;
        $this->_objectManager  = $objectManager;
    }

    /**
     * @param $str
     * @return string
     * @throws \Exception
     */
    public function filter( $str )
    {
        $html = $this->_filterProvider->getPageFilter()->filter( $str );

        return $html;
    }


    public function getConfig( $key, $store = null )
    {
        $store     = $this->storeManager->getStore( $store );
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'advancedreports/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        return $result;
    }

    public function getScopeConfigValue( $key, $store = null )
    {
        $store     = $this->storeManager->getStore( $store );
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        return $result;
    }

    public function getSettingsSkin( $settings = [] )
    {
        $target_selector = isset( $settings['target'] ) ? $settings['target'] : '';
        $html            = '
            <li><a href="javascript:;" class="change-skin" data-target="' . $target_selector . '" data-skin="panel-default">' . __( "Skin Default" ) . '</a>
            </li>
            <li><a href="javascript:;" class="change-skin" data-target="' . $target_selector . '" data-skin="panel-dark">' . __( "Skin Dark" ) . '</a>
            </li>
            <li><a href="javascript:;" class="change-skin" data-target="' . $target_selector . '" data-skin="panel-blue">' . __( "Skin Blue" ) . '</a>
            </li>
        ';

        return $html;
    }

    /**
     * Get report month's amount totals
     *
     * @return mixed
     */
    public function prepareCollection( $request_params = [] )
    {
        if ( ! $request_params ) {
            foreach ( $request_params as $key => $val ) {
                if ( $key && $val ) {
                    $this->_request->setParam( $key, $val );
                }
            }
        }
        $isFilter = $this->_request->getParam( 'store' ) || $this->_request->getParam( 'website' ) || $this->_request->getParam( 'group' );
        $period   = $this->_request->getParam( 'period', '24h' );

        /* @var $collection Mage_Reports_Model_Mysql4_Order_Collection */
        $collection = $this->_objectManager->create( 'Magento\Reports\Model\ResourceModel\Order\Collection' );

        $collection->checkIsLive( $period );

        if ( $collection->isLive() ) {
            $fieldToFilter = 'created_at';
        } else {
            $fieldToFilter = 'period';
        }
        $collection->addFieldToFilter( $fieldToFilter, [
            'from' => $this->getStartMonth(),
            'to'   => $this->getCurrentDate(),
        ] )
                   ->calculateTotals( $isFilter );

        if ( $this->_request->getParam( 'store' ) ) {
            $collection->addFieldToFilter( 'store_id', $this->_request->getParam( 'store' ) );
        } elseif ( $this->_request->getParam( 'website' ) ) {
            $storeIds = $this->storeManager->getWebsite( $this->_request->getParam( 'website' ) )->getStoreIds();
            $collection->addFieldToFilter( 'store_id', [ 'in' => $storeIds ] );
        } elseif ( $this->_request->getParam( 'group' ) ) {
            $storeIds = $this->storeManager->getGroup( $this->_request->getParam( 'group' ) )->getStoreIds();
            $collection->addFieldToFilter( 'store_id', [ 'in' => $storeIds ] );
        } elseif ( ! $collection->isLive() ) {
            $collection->addFieldToFilter( 'store_id',
                [ 'eq' => $this->storeManager->getStore( 'admin' )->getId() ]
            );
        }

        $collection->load();
        $totals = $collection->getFirstItem();

        return $totals;
    }

    /**
     * Get report month's amount totals
     *
     * @return mixed
     */
    public function prepareLastMonthCollection( $request_params = [] )
    {
        if ( ! $request_params ) {
            foreach ( $request_params as $key => $val ) {
                if ( $key && $val ) {
                    $this->_request->setParam( $key, $val );
                }
            }
        }
        $isFilter = $this->_request->getParam( 'store' ) || $this->_request->getParam( 'website' ) || $this->_request->getParam( 'group' );
        $period   = $this->_request->getParam( 'period', '24h' );

        /* @var $collection Mage_Reports_Model_Mysql4_Order_Collection */
        $collection = $this->_objectManager->create( 'Magento\Reports\Model\ResourceModel\Order\Collection' );

        $collection->checkIsLive( $period );

        if ( $collection->isLive() ) {
            $fieldToFilter = 'created_at';
        } else {
            $fieldToFilter = 'period';
        }
        $collection->addFieldToFilter( $fieldToFilter, [
            'from' => $this->getStartLastMonth(),
            'to'   => $this->getEndLastDate(),
        ] )
                   ->calculateTotals( $isFilter );
        if ( $this->_request->getParam( 'store' ) ) {
            $collection->addFieldToFilter( 'store_id', $this->_request->getParam( 'store' ) );
        } elseif ( $this->_request->getParam( 'website' ) ) {
            $storeIds = $this->storeManager->getWebsite( $this->_request->getParam( 'website' ) )->getStoreIds();
            $collection->addFieldToFilter( 'store_id', [ 'in' => $storeIds ] );
        } elseif ( $this->_request->getParam( 'group' ) ) {
            $storeIds = $this->storeManager->getGroup( $this->_request->getParam( 'group' ) )->getStoreIds();
            $collection->addFieldToFilter( 'store_id', [ 'in' => $storeIds ] );
        } elseif ( ! $collection->isLive() ) {
            $collection->addFieldToFilter( 'store_id',
                [ 'eq' => $this->storeManager->getStore( 'admin' )->getId() ]
            );
        }

        $collection->load();
        $totals = $collection->getFirstItem();

        return $totals;
    }

    /**
     * Return current day
     *
     * @return string
     */
    public function getCurrentDate()
    {
        $date = date( 'Y-m-d' );

        return (string) $date . " 23:59:59";
    }

    /**
     * Return first day for current date
     *
     * @return string
     */
    public function getStartMonth()
    {
        $startCurrentMonth = date( 'Y' ) . '-' . date( 'm' ) . '-01';

        return (string) $startCurrentMonth;
    }

    /**
     * Return current day
     *
     * @return string
     */
    public function getEndLastDate()
    {
        $date = date( "Y-m-d", mktime( 0, 0, 0, date( "m" ), 0, date( "Y" ) ) );

        return (string) $date . " 23:59:59";
    }

    /**
     * Return first day for current date
     *
     * @return string
     */
    public function getStartLastMonth()
    {
        $startLastMonth = date( "Y-m-d", mktime( 0, 0, 0, date( "m" ) - 1, 1, date( "Y" ) ) );

        return (string) $startLastMonth . " 00:00:00";
    }

    /**
     * @param        $week
     * @param string $dateFormat
     * @return array
     */
    public function getWeekRange( $week, $dateFormat = "Y-m-d" )
    {
        $week = (int) $week;
        $year = substr( $week, 0, 4 );
        $week = substr( $week, 4, 2 );

        if ( (int) $week < 10 ) {
            $week = "0" . (int) $week;
        }
        if ( $week == "01" ) {
            $previous_year = (int) $year - 1;

        }
        $from = date( $dateFormat, strtotime( "{$year}-W{$week}-0" ) ); //Returns the date of monday in week
        $to   = date( $dateFormat, strtotime( "{$year}-W{$week}-6" ) );   //Returns the date of sunday in week

        return [ $from, $to ];
    }

    public function getReportItem( $report_key = "" )
    {
        $report_types = $this->getReportTypes();

        $return = null;
        foreach ( $report_types as $item ) {
            if ( $item['value'] == $report_key ) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    public function getReportTypes()
    {
        return [
            [ 'value' => "earning", 'path' => "Earning", "label" => __( 'Earning' ) ],
            [ 'value' => "detailed", 'path' => "Order_Detailed", "label" => __( 'Order Detailed Report' ) ],
            [ 'value' => "guestorders", 'path' => "Order_Guestorders", "label" => __( 'Order By Guests Report' ) ],
            [ 'value' => "abandoned", 'path' => "Order_Abandoned", "label" => __( 'Abandoned Carts' ) ],
            [ 'value' => "abandoneddetailed", 'path' => "Order_Abandoneddetailed", "label" => __( 'Abandoned Detailed Carts' ) ],
            [ 'value' => "itemsdetailed", 'path' => "Order_Itemsdetailed", "label" => __( 'Order Items Detailed Report' ) ],
            [ 'value' => "activity", 'path' => "Customer_Activity", "label" => __( 'Customer Activity Report' ) ],
            [ 'value' => "customersreport", 'path' => "Customer_Customersreport", "label" => __( 'Customer Report' ) ],
            [ 'value' => "topcustomers", 'path' => "Customer_Topcustomers", "label" => __( 'Top Customer Report' ) ],
            [ 'value' => "productscustomer", 'path' => "Customer_Productscustomer", "label" => __( 'Products Customer Report' ) ],
            [ 'value' => "customerscity", 'path' => "Customer_Customerscity", "label" => __( 'Customers by City' ) ],
            [ 'value' => "customerscountry", 'path' => "Customer_Customerscountry", "label" => __( 'Customers by Country' ) ],
            [ 'value' => "customernotorder", 'path' => "Customer_Customernotorder", "label" => __( 'Customers Not Order' ) ],
            [ 'value' => "productsreport", 'path' => "Products_Productsreport", "label" => __( 'Products Report' ) ],
            [ 'value' => "productsnotsold", 'path' => "Products_Productsnotsold", "label" => __( 'Products Not Sold' ) ],
            [ 'value' => "overview", 'path' => "Sales_Overview", "label" => __( 'Sales Overview' ) ],
            [ 'value' => "statistics", 'path' => "Sales_Statistics", "label" => __( 'Sales Statistics' ) ],
            [ 'value' => "customergroup", 'path' => "Sales_Customergroup", "label" => __( 'Sales By Customer Group' ) ],
            [ 'value' => "producttype", 'path' => "Sales_Producttype", "label" => __( 'Sales Product Type' ) ],
            [ 'value' => "hour", 'path' => "Sales_Hour", "label" => __( 'Sales by Hour' ) ],
            [ 'value' => "dayofweek", 'path' => "Sales_Dayofweek", "label" => __( 'Sales by Day Of Week' ) ],
            [ 'value' => "product", 'path' => "Sales_Product", "label" => __( 'Sales By Product' ) ],
            [ 'value' => "category", 'path' => "Sales_Category", "label" => __( 'Sales Category' ) ],
            [ 'value' => "paymenttype", 'path' => "Sales_Paymenttype", "label" => __( 'Sales By Payment Type' ) ],
            [ 'value' => "country", 'path' => "Sales_Country", "label" => __( 'Sales By Country' ) ],
            [ 'value' => "region", 'path' => "Sales_Region", "label" => __( 'Sales By Region/State' ) ],
            [ 'value' => "zipcode", 'path' => "Sales_Zipcode", "label" => __( 'Sales By Zipcode' ) ],
            [ 'value' => "coupon", 'path' => "Sales_Coupon", "label" => __( 'Sales By Coupon' ) ],
            [ 'value' => "manage_customer", "label" => __( 'Manage Customer' ) ],
            [ 'value' => "manage_order", "label" => __( 'Manage Order' ) ],
        ];
    }
}

