/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */
(function($,sr){
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
      var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap)
                    func.apply(obj, args); 
                timeout = null; 
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100); 
        };
    };

    // smartresize 
    jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var CURRENT_URL = window.location.href.split('#')[0].split('?')[0],
    $BODY = jQuery('body'),
    $MENU_TOGGLE = jQuery('#menu_toggle'),
    $SIDEBAR_MENU = jQuery('#sidebar-menu'),
    $SIDEBAR_FOOTER = jQuery('.sidebar-footer'),
    $LEFT_COL = jQuery('.left_col'),
    $RIGHT_COL = jQuery('.right_col'),
    $NAV_MENU = jQuery('.nav_menu'),
    $FOOTER = jQuery('footer');


// Panel toolbox
jQuery(document).ready(function() {
    jQuery('.collapse-link').on('click', function() {
        var $BOX_PANEL = jQuery(this).closest('.x_panel'),
            $ICON = jQuery(this).find('i'),
            $BOX_CONTENT = $BOX_PANEL.find('.x_content');
        
        // fix for some div with hardcoded fix class
        if ($BOX_PANEL.attr('style')) {
            $BOX_CONTENT.slideToggle(200, function(){
                $BOX_PANEL.removeAttr('style');
            });
        } else {
            $BOX_CONTENT.slideToggle(200); 
            $BOX_PANEL.css('height', 'auto');  
        }

        $ICON.toggleClass('fa-chevron-up fa-chevron-down');
    });

    jQuery('.close-link').click(function () {
        var $BOX_PANEL = jQuery(this).closest('.x_panel');

        $BOX_PANEL.remove();
    });
});
// /Panel toolbox

// Tooltip
// jQuery(document).ready(function() {
//     jQuery('[data-toggle="tooltip"]').tooltip({
//         container: 'body'
//     });
// });
// /Tooltip

// Progressbar
if (jQuery(".progress .progress-bar")[0]) {
    jQuery('.progress .progress-bar').progressbar();
}
// /Progressbar

// Switchery
jQuery(document).ready(function() {
    if (jQuery(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html, {
                color: '#26B99A'
            });
        });
    }
});
// /Switchery

// iCheck
jQuery(document).ready(function() {
    if (jQuery("input.flat")[0]) {
        jQuery(document).ready(function () {
            jQuery('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
});
// /iCheck

// Table
jQuery('table input').on('ifChecked', function () {
    checkState = '';
    jQuery(this).parent().parent().parent().addClass('selected');
    countChecked();
});
jQuery('table input').on('ifUnchecked', function () {
    checkState = '';
    jQuery(this).parent().parent().parent().removeClass('selected');
    countChecked();
});

var checkState = '';

jQuery('.bulk_action input').on('ifChecked', function () {
    checkState = '';
    jQuery(this).parent().parent().parent().addClass('selected');
    countChecked();
});
jQuery('.bulk_action input').on('ifUnchecked', function () {
    checkState = '';
    jQuery(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
jQuery('.bulk_action input#check-all').on('ifChecked', function () {
    checkState = 'all';
    countChecked();
});
jQuery('.bulk_action input#check-all').on('ifUnchecked', function () {
    checkState = 'none';
    countChecked();
});

function countChecked() {
    if (checkState === 'all') {
        jQuery(".bulk_action input[name='table_records']").iCheck('check');
    }
    if (checkState === 'none') {
        jQuery(".bulk_action input[name='table_records']").iCheck('uncheck');
    }

    var checkCount = jQuery(".bulk_action input[name='table_records']:checked").length;

    if (checkCount) {
        jQuery('.column-title').hide();
        jQuery('.bulk-actions').show();
        jQuery('.action-cnt').html(checkCount + ' Records Selected');
    } else {
        jQuery('.column-title').show();
        jQuery('.bulk-actions').hide();
    }
}

// Accordion
jQuery(document).ready(function() {
    jQuery(".expand").on("click", function () {
        jQuery(this).next().slideToggle(200);
        $expand = jQuery(this).find(">:first-child");

        if ($expand.text() == "+") {
            $expand.text("-");
        } else {
            $expand.text("+");
        }
    });
    if(jQuery(".change-skin").length > 0) {
        jQuery(".change-skin").on("click", function () {
            $target = jQuery(this).data("target");
            $skin = jQuery(this).data("skin");

            if($target && jQuery($target).length > 0) {
                $currentSkin = jQuery($target).data("currentSkin");
                jQuery($target).removeClass($currentSkin);
                jQuery($target).data("currentSkin", $skin);
                jQuery($target).addClass($skin);
            }
        });
    }
});
