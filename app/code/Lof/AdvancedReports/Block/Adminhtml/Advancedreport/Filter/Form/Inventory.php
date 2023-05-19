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
class Inventory extends \Lof\AdvancedReports\Block\Adminhtml\Advancedreport\Filter\Form
{
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl($this->getFormActionUrl());
        $report_type = $this->getReportType();
        $notshow_actual = array("productsnotsold");

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
        $htmlIdPrefix = 'inventory_report_';
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

        $fieldset->addField('store_ids', 'hidden', array(
            'name'  => 'store_ids'
        ));

        $fieldset->addField('currency_code', 'hidden', array(
            'name'  => 'currency_code'
        ));

        $fieldset->addField('show_order_statuses', 'select', [
            'name'      => 'show_order_statuses',
            'label'     => __('Order Status'),
            'options'   => array(
                '0' => __('Any'),
                '1' => __('Specified'),
            ),
            'note'      => __('Applies to Any of the Specified Order Statuses'),
        ], 'to');

        $fieldset->addField('order_statuses', 'multiselect',[
            'name'      => 'order_statuses',
            'values'    => $values,
            'label'     => __('Status'),
            'display'   => 'none'
        ], 'show_order_statuses');

            // define field dependencies
        if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
            $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
                ->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
                ->addFieldDependence('order_statuses', 'show_order_statuses', '1')

            );
        }

        $fieldset->addField("category_ids", "text", array(
            "label" => __("Category Ids"),
            "class" => "form-control",
            "name"  => "category_ids",
            "note" => __("Input single category Id or multi category Ids. For multi: just use comma as this: Id1,Id2,Id3,..")
        ));

        $fieldset->addField("product_sku", "text", array(
            "label" => __("Product SKU"),
            "name" => "product_sku",
            "note" => __("Filter product by sku")
        ));

        $fieldset->addField("qty_from", "text", array(
            "label" => __("Stock Quantity From"),
            "name" => "qty_from",
            "note" => __("Input stock qty from to filter")
        ));

        $fieldset->addField("qty_to", "text", array(
            "label" => __("Stock Quantity To"),
            "name" => "qty_to",
            "note" => __("Input stock qty to to filter")
        ));


        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
