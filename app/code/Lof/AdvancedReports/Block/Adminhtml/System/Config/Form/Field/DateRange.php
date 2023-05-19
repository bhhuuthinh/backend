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
namespace Lof\AdvancedReports\Block\Adminhtml\System\Config\Form\Field;
class DateRange extends \Magento\Framework\Data\Form\Element\AbstractElement
{
  public function _construct($attributes=array())
  {
    parent::_construct($attributes);
    $this->setType('text');
    $this->setExtType('textfield'); 
    $this->setLabelStyle('float:left;margin-left:30px;padding:5px;');  
  }

  public function getElementHtml()
  {
    $params = array();
    $params['value'] = $this->getEscapedValue();
    $params['model'] = $this->getModelData();
    $id = $this->getBlockId();
    $minDate = $this->getMinDate();
    $minDate = $minDate?$minDate:"01/01/1975";
    $maxDate = $this->getMaxDate();
    $maxDate = $maxDate?$maxDate:"12/31/2030";

    $startDate = $this->getStartDate();
    $endDate = $this->getEndDate();
    $targetFromSelector = $this->getTargetFrom();
    $targetToSelector = $this->getTargetTo();
    
    $options = array("id" => $id,
      "minDate" => $minDate,
      "maxDate" => $maxDate,
      "startDate" => $startDate,
      "endDate" => $endDate,
      "open"  => "right",
      "targetFromSelector" => $targetFromSelector,
      "targetToSelector" => $targetToSelector
      );

    $html = '<div id="'.$id.'" class="custom-date-range" style="'.$this->getFieldStyle().'">
    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
    <span>'.$this->getDefaultValue().'</span> <i class="fa fa-caret-down" aria-hidden="true"></i> 
  </div>'; 
  $html .= $this->initScripts($options);
  $html.= $this->getAfterElementHtml();
  return $html;
}

public function getLabelHtml($idSuffix = '', $scopeLabel = ''){
  if (!is_null($this->getLabel())) {
    $html = '<label for="'.$this->getHtmlId() . $idSuffix . '" class="label admin__field-label"  >'.$this->getLabel()
    . ( $this->getRequired() ? ' <span class="required"> </span>' : '' ).'</label>'."\n";
  }
  else {
    $html = '';
  }
  return $html;
}

protected function initScripts($options = array()) {
        //Init params in options
  $field_id = isset($options['id'])?$options['id']:"";
  $minDate = isset($options['minDate'])?$options['minDate']:"01/01/1975";
  $maxDate = isset($options['maxDate'])?$options['maxDate']:"12/31/2030";
  $startDate = isset($options['startDate'])?$options['startDate']:"";
  $endDate = isset($options['endDate'])?$options['endDate']:"";
  $open = isset($options['open'])?$options['open']:"right";
  $targetFromSelector = isset($options['targetFromSelector'])?$options['targetFromSelector']:"date_from";
  $targetToSelector = isset($options['targetToSelector'])?$options['targetToSelector']:"date_to";
        //End Init params in options

  $startDateString = 'var $startDateString = moment().subtract(29, "days").format("MMMM D, YYYY");';
  $endDateString = 'var $endDateString = moment().format("MMMM D, YYYY");';

  if(!$startDate) {
    $startDate = 'moment().subtract(29, "days")';
  } else {
          $tmp = $startDate/1000;//Convert to php datetime
          $tmp_obj = new \Zend_Date($tmp);
          $startDateString = 'var $startDateString ="'.$tmp_obj->toString('MMMM dd, yyyy').'";';
          $startDate = 'moment('.$startDate.')';
        }
        if(!$endDate) {
          $endDate = 'moment()';
        }else{
          $tmp = $endDate/1000;//Convert to php datetime
          $tmp_obj = new \Zend_Date($tmp);
          $endDateString = 'var $endDateString ="'.$tmp_obj->toString('MMMM dd, yyyy').'";';
          $endDate = 'moment('.$endDate.')';
        } 
        $html = ' 
        <script>
          require(["jquery",
          "Lof_AdvancedReports/vendors/moment/min/moment.min",
          "Lof_AdvancedReports/vendors/bootstrap-daterangepicker/daterangepicker"
          ], function(jQuery, moment){ 
            jQuery(document).ready(function() {
              var cb = function(start, end, label) { 
                jQuery("#'.$field_id.' span").html(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
              };
              var $startDate = '.$startDate.';
              var $endDate = '.$endDate.';
              '.$startDateString.$endDateString.'
              var optionSet1 = {
                startDate: $startDate,
                endDate: $endDate,
                minDate: "'.$minDate.'",
                maxDate: "'.$maxDate.'",
                dateLimit: {
                  days: 2000
                },
                showDropdowns: true,
                showWeekNumbers: true,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                  "'.__('Today').'": [moment(), moment()],
                  "'.__('Yesterday').'": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                  "'.__('Last 7 Days').'": [moment().subtract(6, "days"), moment()],
                  "'.__('Last 30 Days').'": [moment().subtract(29, "days"), moment()],
                  "'.__('This Month').'": [moment().startOf("month"), moment().endOf("month")],
                  "'.__('Last Month').'": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
                },
                opens: "'.$open.'",
                buttonClasses: ["btn btn-default"],
                applyClass: "btn-small btn-primary",
                cancelClass: "btn-small",
                format: "MM/DD/YYYY",
                separator: "'.__(' to ').'",
                locale: {
                  applyLabel: "'.__('Submit').'",
                  cancelLabel: "'.__('Clear').'",
                  fromLabel: "'.__('From').'",
                  toLabel: "'.__('To').'",
                  customRangeLabel: "'.__('Custom').'",
                  daysOfWeek: ["'.__('Su').'", "'.__('Mo').'", "'.__('Tu').'", "'.__('We').'", "'.__('Th').'", "'.__('Fr').'", "'.__('Sa').'"],
                  monthNames: ["'.__('January').'", "'.__('February').'", "'.__('March').'", "'.__('April').'", "'.__('May').'", "'.__('June').'", "'.__('July').'", "'.__('August').'", "'.__('September').'", "'.__('October').'", "'.__('November').'", "'.__('December').'"],
                  firstDay: 1
                }
              };

              if(jQuery("'.$targetFromSelector.'").length > 0){
                jQuery("'.$targetFromSelector.'").first().val($startDate.format("M/D/YYYY"));
              }
              if(jQuery("'.$targetToSelector.'").length > 0){
                jQuery("'.$targetToSelector.'").first().val($endDate.format("M/D/YYYY"));
              }
              jQuery("#'.$field_id.' span").html($startDateString + " - " + $endDateString);
              jQuery("#'.$field_id.'").daterangepicker(optionSet1, cb);
              jQuery("#'.$field_id.'").on("show.daterangepicker", function() {
                console.log("'.__('show event fired').'");
              });
              jQuery("#'.$field_id.'").on("hide.daterangepicker", function() {
                console.log("'.__('hide event fired').'");
              });
              jQuery("#'.$field_id.'").on("apply.daterangepicker", function(ev, picker) {
                console.log("'.__('apply event fired, start/end dates are').'" + picker.startDate.format("MMMM D, YYYY") + "'.__(' to ').'" + picker.endDate.format("MMMM D, YYYY"));
                
                if(jQuery("'.$targetFromSelector.'").length > 0){
                  jQuery("'.$targetFromSelector.'").first().val(picker.startDate.format("M/D/YYYY"));
                }
                if(jQuery("'.$targetToSelector.'").length > 0){
                  jQuery("'.$targetToSelector.'").first().val(picker.endDate.format("M/D/YYYY"));
                }
              });
              jQuery("#'.$field_id.'").on("cancel.daterangepicker", function(ev, picker) {
                console.log("'.__('cancel event fired').'");
              });
              jQuery("#options1").click(function() {
                jQuery("#'.$field_id.'").data("daterangepicker").setOptions(optionSet1, cb);
              });
              jQuery("#options2").click(function() {
                jQuery("#'.$field_id.'").data("daterangepicker").setOptions(optionSet2, cb);
              });
              jQuery("#destroy").click(function() {
                jQuery("#'.$field_id.'").data("daterangepicker").remove();
              });
            });
          });
        </script>
        ';

        return $html;
      }
    }