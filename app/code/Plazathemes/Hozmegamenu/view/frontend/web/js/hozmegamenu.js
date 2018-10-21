require(["jquery"], function(jQuery){

    jQuery("#pt_menu_link ul li").each(function(){
        var url = document.URL;
        jQuery("#pt_menu_link ul li a").removeClass("act");
        jQuery('#pt_menu_link ul li a[href="'+url+'"]').addClass('act');
    }); 
    
    jQuery('.pt_menu_no_child').hover(function(){
        jQuery(this).addClass("active");
    },function(){
        jQuery(this).removeClass("active");
    })
    
    jQuery('.pt_menu').hover(function(){
        if(jQuery(this).attr("id") != "pt_menu_link"){
            jQuery(this).addClass("active");
        }
    },function(){
        jQuery(this).removeClass("active");
    })
    
    jQuery('.pt_menu').hover(function(){
       /*show popup to calculate*/
       jQuery(this).find('.popup').css('display','inline-block');
       
       /* get total padding + border + margin of the popup */
       var extraWidth       = 0
       var wrapWidthPopup   = jQuery(this).find('.popup').outerWidth(true); /*include padding + margin + border*/
       var actualWidthPopup = jQuery(this).find('.popup').width(); /*no padding, margin, border*/
       extraWidth           = wrapWidthPopup - actualWidthPopup;    
       
       /* calculate new width of the popup*/
       var widthblock1 = jQuery(this).find('.popup .block1').outerWidth(true);
       var widthblock2 = jQuery(this).find('.popup .block2').outerWidth(true);
       var new_width_popup = 0;
       if(widthblock1 && !widthblock2){
           new_width_popup = widthblock1;
       }
       if(!widthblock1 && widthblock2){
           new_width_popup = widthblock2;
       }
       if(widthblock1 && widthblock2){
            if(widthblock1 >= widthblock2){
                new_width_popup = widthblock1;
            }
            if(widthblock1 < widthblock2){
                new_width_popup = widthblock2;
            }
       }
       var new_outer_width_popup = new_width_popup + extraWidth;
       
       /*define top and left of the popup*/
       var wraper = jQuery('.pt_custommenu');
       var wWraper = wraper.outerWidth();
       var posWraper = wraper.offset();
       var pos = jQuery(this).offset();
       
       var xTop = pos.top - posWraper.top + CUSTOMMENU_POPUP_TOP_OFFSET;
       var xLeft = pos.left - posWraper.left;
       if ((xLeft + new_outer_width_popup) > wWraper) xLeft = wWraper - new_outer_width_popup;

       jQuery(this).find('.popup').css('top',xTop);
       jQuery(this).find('.popup').css('left',xLeft);
       
       /*set new width popup*/
       jQuery(this).find('.popup').css('width',new_width_popup);
       jQuery(this).find('.popup .block1').css('width',new_width_popup);
       
       /*return popup display none*/
       jQuery(this).find('.popup').css('display','none');
       
       /*show hide popup*/
       if(CUSTOMMENU_POPUP_EFFECT == 0) jQuery(this).find('.popup').stop(true,true).slideDown('slow');
       if(CUSTOMMENU_POPUP_EFFECT == 1) jQuery(this).find('.popup').stop(true,true).fadeIn('slow');
       if(CUSTOMMENU_POPUP_EFFECT == 2) jQuery(this).find('.popup').stop(true,true).show();
    },function(){
       if(CUSTOMMENU_POPUP_EFFECT == 0) jQuery(this).find('.popup').stop(true,true).slideUp();
       if(CUSTOMMENU_POPUP_EFFECT == 1) jQuery(this).find('.popup').stop(true,true).fadeOut('slow');
       if(CUSTOMMENU_POPUP_EFFECT == 2) jQuery(this).find('.popup').stop(true,true).hide('fast');
    })

});