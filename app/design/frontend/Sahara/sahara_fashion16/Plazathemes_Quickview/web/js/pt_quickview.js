/*
 * Copyright © 2016 PlazaThemes.com. All rights reserved.
 *
 * @author PlazaThemes Team <contact@plazathemes.com>
 */

//<![CDATA[
var qv_status = true;
var pt_quickview = {
    'ajaxView' : function (url) {
        require(['jquery'], function($){
            $.ajax({
                url         : url,
                method      : "post",
                data        : { qv_action : true },
                dataType    : 'json',
                beforeSend  : function() {
                    pt_quickview.showQuickViewBackground();
                },
                success     : function(data) {
                    console.log(data);
                    if(data.product_info) {
                        $('#quickview-content').html(data.product_info).trigger('contentUpdated');
                    } else {
                        pt_quickview.showErrorNotification();
                        qv_status = false;
                    }
                },
                complete    : function() {
                    if(qv_status == true) {
                        pt_quickview.showQuickViewContent();
                    }
                },
                error       : function() {
                    pt_quickview.showErrorNotification();
                }
            });
        });
    },
    'showQuickViewContent'    : function() {
        require(['jquery'], function($){
            $('.quickview-container').show();
        });
    },
    'showQuickViewBackground' : function() {
        require(['jquery'], function($){
            $('.quickview-background').show().addClass('loading-mask');
            $('.quickview-load-img').show();
        });
    },
    'hideQuickViewBackground' : function() {
        require(['jquery'], function($){
            $('.quickview-background').hide().removeClass('loading-mask');
            $('.quickview-load-img').hide();
            $('.quickview-container').hide();
            $('#quickview-content').html('');
        });
    },
    'showErrorNotification'   : function() {
        require(['jquery'], function($){
            var qv_error = $('.qv-error-notification').val();
            alert(qv_error);
            pt_quickview.hideQuickViewBackground();
        });
    },
    'initQuickViewButton'     : function(container, item, button, link, insertion) {
        require(['jquery'], function($){
            $(container + ' ' + link).each(function() {
                var rel = $(this).attr('rel');
                if(rel == null) {
                    $(this).attr('rel', 'author');
                    var pd_link = $(this).attr('href');
                    var click_evt = 'pt_quickview.ajaxView("'+ pd_link + '")';
                    var qv_btn_text = $('.qv-button-text').val();
                    var button_html = "";
                    button_html += "<button type='button' class='btn-quickview' onclick='"+ click_evt +"' value='"+ qv_btn_text +"'>";
                    button_html += "<span>" + qv_btn_text + "</span>";
                    button_html += "</button>";
                    if(insertion == "after") {
                        $(this).closest(item).find(button).append(button_html);
                    } else {
                        $(this).closest(item).find(button).prepend(button_html);
                    }
                }
            })
        });
    }
};
//]]>
