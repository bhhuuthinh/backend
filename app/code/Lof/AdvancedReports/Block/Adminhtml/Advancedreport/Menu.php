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
 * @category  Landofcoder
 * @package   Lof_Advancedreport
 * @copyright Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport;


/**
 * Class Menu
 *
 * @package Lof\AdvancedReports\Block\Adminhtml\Advancedreport
 */
class Menu extends \Magento\Backend\Block\Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * @var \Lof\CompanyProduct\Helper\Currency
     */
    protected $_currencyHelper;

    /**
     * @var \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory
     */
    protected $_symbolSystemFactory;

    /**
     * Custom currency symbol properties
     *
     * @var array
     */
    protected $_symbolsData = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var null
     */
    protected $_currentCurrencyCode = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Lof_All::menu.phtml';

    /**
     * Menu constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                    $context
     * @param \Lof\AdvancedReports\Helper\Currency                       $currencyHelper
     * @param \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolSystemFactory
     * @param \Magento\Framework\ObjectManagerInterface                  $objectManager
     * @param array                                                      $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context,
        \Lof\AdvancedReports\Helper\Currency $currencyHelper,
        \Magento\CurrencySymbol\Model\System\CurrencysymbolFactory $symbolSystemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context);

        $this->_currencyHelper = $currencyHelper;
        $this->_symbolSystemFactory = $symbolSystemFactory;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
    }

    /**
     * @return array|array[]|null
     */
    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                'advancedreports_earning' => [
                    'title' => __('Earning'),
                    'url' => $this->getUrl('*/advancedreports_earning/earning/current/1'),
                    'resource' => 'Lof_AdvancedReports::earningreport'
                ],
                'advancedreports_order' => [
                    'title' => __('Order Reports'),
                    'url' => '#',
                    'resource' => 'Lof_AdvancedReports::order',
                    'item' => [
                        'detailed' => [
                        'title' => __('Order Detailed Report'),
                        'url' => $this->getUrl('*/advancedreports_order/detailed'),
                        'resource' => 'Lof_AdvancedReports::detailed'
                        ],
                        'guestorders' => [
                        'title' => __('Order By Guests Report'),
                        'url' => $this->getUrl('*/advancedreports_order/guestorders'),
                        'resource' => 'Lof_AdvancedReports::guestorders'
                        ],
                        'abandoned' => [
                        'title' => __('Abandoned Carts'),
                        'url' => $this->getUrl('*/advancedreports_order/abandoned'),
                        'resource' => 'Lof_AdvancedReports::abandoned'
                        ],
                        'abandoneddetailed' => [
                        'title' => __('Abandoned Detailed Carts'),
                        'url' => $this->getUrl('*/advancedreports_order/abandoneddetailed'),
                        'resource' => 'Lof_AdvancedReports::abandoneddetailed'
                        ],
                        'itemsdetailed' => [
                        'title' => __('Order Items Detailed Report'),
                        'url' => $this->getUrl('*/advancedreports_order/itemsdetailed'),
                        'resource' => 'Lof_AdvancedReports::itemsdetailed'
                        ]
                    ]

                ],
                'advancedreports_customer' => [
                    'title' => __('Customer Reports'),
                    'url' => '#',
                    'resource' => 'Lof_AdvancedReports::customers',
                    'item' => [
                        'activity' => [
                        'title' => __('Customer Activity Report'),
                        'url' => $this->getUrl('*/advancedreports_customer/activity'),
                        'resource' => 'Lof_AdvancedReports::customeractivity'
                        ],
                        'customersreport' => [
                        'title' => __('Customer Report'),
                        'url' => $this->getUrl('*/advancedreports_customer/customersreport'),
                        'resource' => 'Lof_AdvancedReports::customersreport'
                        ],
                        'topcustomers' => [
                        'title' => __('Top Customer Report'),
                        'url' => $this->getUrl('*/advancedreports_customer/topcustomers'),
                        'resource' => 'Lof_AdvancedReports::topcustomers'
                        ],
                        'productscustomer' => [
                        'title' => __('Products Customer Report'),
                        'url' => $this->getUrl('*/advancedreports_customer/productscustomer'),
                        'resource' => 'Lof_AdvancedReports::productscustomer'
                        ],
                        'customerscity' => [
                        'title' => __('Customers by City'),
                        'url' => $this->getUrl('*/advancedreports_customer/customerscity'),
                        'resource' => 'Lof_AdvancedReports::customerscity'
                        ],
                        'customerscountry' => [
                        'title' => __('Customers by Country'),
                        'url' => $this->getUrl('*/advancedreports_customer/customerscountry'),
                        'resource' => 'Lof_AdvancedReports::customerscountry'
                        ],
                        'customernotorder' => [
                        'title' => __('Customers Not Order'),
                        'url' => $this->getUrl('*/advancedreports_customer/customernotorder'),
                        'resource' => 'Lof_AdvancedReports::customernotorder'
                        ]
                    ]

                ],
                'advancedreports_products' => [
                    'title' => __('Product Reports'),
                    'url' => '#',
                    'resource' => 'Lof_AdvancedReports::products',
                    'item' => [
                        'productsreport' => [
                        'title' => __('Products Report'),
                        'url' => $this->getUrl('*/advancedreports_products/productsreport'),
                        'resource' => 'Lof_AdvancedReports::productsreport'
                        ],
                        'productsnotsold' => [
                        'title' => __('Products Not Sold'),
                        'url' => $this->getUrl('*/advancedreports_products/productsnotsold'),
                        'resource' => 'Lof_AdvancedReports::productsnotsold'
                        ]
                        ,
                        'inventory' => [
                        'title' => __('Product - Inventory Reports'),
                        'url' => $this->getUrl('*/advancedreports_products/inventory'),
                        'resource' => 'Lof_AdvancedReports::inventory'
                        ]
                        ,
                        'productsoldtogether' => [
                        'title' => __('Most Products Sold Together'),
                        'url' => $this->getUrl('*/advancedreports_products/productsoldtogether'),
                        'resource' => 'Lof_AdvancedReports::productsoldtogether'
                        ]
                    ]

                ],
                'advancedreports_sales' => [
                    'title' => __('Sales Reports'),
                    'url' => '#',
                    'resource' => 'Lof_AdvancedReports::sales',
                    'item' => [
                        'overview' => [
                        'title' => __('Sales Overview'),
                        'url' => $this->getUrl('*/advancedreports_sales/overview'),
                        'resource' => 'Lof_AdvancedReports::overview'
                        ],
                        'statistics' => [
                        'title' => __('Sales Statistics'),
                        'url' => $this->getUrl('*/advancedreports_sales/statistics'),
                        'resource' => 'Lof_AdvancedReports::salesstatistics    '
                        ],
                        'customergroup' => [
                        'title' => __('Sales By Customer Group'),
                        'url' => $this->getUrl('*/advancedreports_sales/customergroup'),
                        'resource' => 'Lof_AdvancedReports::salesbycustomergroup'
                        ],
                        'producttype' => [
                        'title' => __('Sales Product Type'),
                        'url' => $this->getUrl('*/advancedreports_sales/producttype'),
                        'resource' => 'Lof_AdvancedReports::salesbyproducttype'
                        ],
                        'hour' => [
                        'title' => __('Sales by Hour'),
                        'url' => $this->getUrl('*/advancedreports_sales/hour'),
                        'resource' => 'Lof_AdvancedReports::salesbyhour'
                        ],
                        'dayofweek' => [
                        'title' => __('Sales by Day Of Week'),
                        'url' => $this->getUrl('*/advancedreports_sales/salesbydayofweek'),
                        'resource' => 'Lof_AdvancedReports::dayofweek'
                        ],
                        'product' => [
                        'title' => __('Sales By Product'),
                        'url' => $this->getUrl('*/advancedreports_sales/product'),
                        'resource' => 'Lof_AdvancedReports::product'
                        ],
                        'category' => [
                        'title' => __('Sales Category'),
                        'url' => $this->getUrl('*/advancedreports_sales/category'),
                        'resource' => 'Lof_AdvancedReports::salesbycategory'
                        ],
                        'paymenttype' => [
                        'title' => __('Sales By Payment Type'),
                        'url' => $this->getUrl('*/advancedreports_sales/paymenttype'),
                        'resource' => 'Lof_AdvancedReports::salesbypaymenttype'
                        ],
                        'country' => [
                        'title' => __('Sales By Country'),
                        'url' => $this->getUrl('*/advancedreports_sales/country'),
                        'resource' => 'Lof_AdvancedReports::salesbycountry'
                        ],
                        'productcountry' => [
                        'title' => __('Sales Per Product Per Country'),
                        'url' => $this->getUrl('*/advancedreports_sales/productcountry'),
                        'resource' => 'Lof_AdvancedReports::salesproductcountry'
                        ],
                        'region' => [
                        'title' => __('Sales By Region/State'),
                        'url' => $this->getUrl('*/advancedreports_sales/region'),
                        'resource' => 'Lof_AdvancedReports::salesbyregion'
                        ],
                        'zipcode' => [
                        'title' => __('Sales By Zipcode'),
                        'url' => $this->getUrl('*/advancedreports_sales/zipcode'),
                        'resource' => 'Lof_AdvancedReports::salesbyzipcode'
                        ],
                        'coupon' => [
                        'title' => __('Sales By Coupon'),
                        'url' => $this->getUrl('*/advancedreports_sales/coupon'),
                        'resource' => 'Lof_AdvancedReports::salesbycoupon'
                        ]
                    ]

                ],
            ];

            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }
            $this->items = $items;
        }

        return $this->items;
    }

    /**
     * @return mixed
     */
    public function getCurrentItem()
    {
        $items          = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName];
        }

        return $items['page'];

    }//end getCurrentItem()

    /**
     * @param null $currency_code
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentCurrencyCode( $currency_code = null )
    {
        if ( $currency_code ) {
            return $currency_code;
        }

        $requestData = $this->_objectManager->get(
            'Magento\Backend\Helper\Data'
        )->prepareFilterString(
            $this->getRequest()->getParam('loffilter')
        );

        $requestCurrency = isset($requestData['currency_code']) && $requestData['currency_code'] ? $requestData['currency_code'] : '';
        if ( $requestCurrency ) {
            return $requestCurrency;
        }

        if ( $this->_currentCurrencyCode === null ) {
            $storeIds =$this->getStoreIds();
            if ( ! $storeIds ) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
            }

            if ( count($storeIds) == 1 ) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore($storeIds[0])->getBaseCurrencyCode();;
            }

            if ( count($storeIds) > 1 ) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
            }
        }

        return $this->_currentCurrencyCode;
    }

    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return  array
     */
    protected function getStoreIds()
    {
        if ( $this->getRequest()->getParam( 'store_ids' ) ) {
            $storeIds = explode( ',', $this->getRequest()->getParam( 'store_ids' ) );
        } else {
            $storeIds = [];
        }
        // By default storeIds array contains only allowed stores
        $allowedStoreIds = array_keys( $this->_storeManager->getStores() );
        // And then array_intersect with post data for prevent unauthorized stores reports
        $storeIds = array_intersect( $allowedStoreIds, $storeIds );
        // If selected all websites or unauthorized stores use only allowed
        if ( empty( $storeIds ) ) {
            $storeIds = $allowedStoreIds;
        }
        // reset array keys
        $storeIds = array_values( $storeIds );

        return $storeIds;
    }

    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }

        return $result;

    }//end renderAttributes()

    /**
     * @param $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        return $itemIndex == $this->getRequest()->getControllerName();

    }//end isCurrent()

    /**
     * @return \Magento\Directory\Model\Currency
     */
    public function getCurrency()
    {
        return $this->_currencyHelper->getCurrency();
    }

    /**
     * @return \Lof\AdvancedReports\Helper\Currency
     */
    public function getCurrencyHelper()
    {
        return $this->_currencyHelper;
    }

    /**
     * Returns Custom currency symbol properties
     *
     * @return array
     */
    public function getCurrencySymbolsData()
    {
        if (!$this->_symbolsData) {
            $this->_symbolsData = $this->_symbolSystemFactory->create()->getCurrencySymbolsData();
        }
        return $this->_symbolsData;
    }

}//end class
