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
namespace Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Filter\Form;

/**
 * Sales Adminhtml report filter form order
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Earning extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Filter\Form
{
    /**
     * Preparing form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */


    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/earning');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'filter_form',
                    'action' => $actionUrl,
                    'method' => 'get'
                ]
            ]
        );

        $htmlIdPrefix = 'earning_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Filter')]);
        $statuses = $this->_objectManager->create('Magento\Sales\Model\Order\Config')->getStatuses();
        $values = array();
        foreach ($statuses as $code => $label) {
            if (false === strpos($code, 'pending')) {
                $values[] = array(
                    'label' => __($label),
                    'value' => $code
                );
            }
        }

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $fieldset->addField('store_ids', 'hidden', ['name' => 'store_ids']);
        $fieldset->addField('currency_code', 'hidden', array(
            'name'  => 'currency_code'
        ));
        $fieldset->addField('filter_year', 'hidden', [
            'name'  => 'filter_year'
        ]);

        $fieldset->addField('filter_month', 'hidden', [
            'name'  => 'filter_month'
        ]);

        $fieldset->addField('filter_day', 'hidden', [
            'name'  => 'filter_day'
        ]);
        $fieldset->addField('current', 'hidden', [
            'name'  => 'current'
        ]);


        $fieldset->addField('show_order_statuses', 'select', [
            'name'      => 'show_order_statuses',
            'label'     =>  __('Order Status'),
            'options'   => [
                    '0' =>  __('Any'),
                    '1' =>  __('Specified'),
                ],
            'note'      =>  __('Applies to Any of the Specified Order Statuses'),
        ], 'to');

        $fieldset->addField('order_statuses', 'multiselect', [
            'name'      => 'order_statuses',
            'label'     => __('Status'),
            'values'    => $values,
            'display'   => 'none'
        ], 'show_order_statuses');

        //define field dependencies
        if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
            $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                    ->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
                    ->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
                    ->addFieldDependence('order_statuses', 'show_order_statuses', '1')

            );
        }


        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
