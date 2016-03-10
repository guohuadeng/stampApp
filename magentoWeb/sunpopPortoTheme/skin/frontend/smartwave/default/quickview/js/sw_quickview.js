jQuery.noConflict();
jQuery(function($) {
    //insert quickview popup        
    $('.quickview-icon').fancybox({
        'type'              : 'iframe',
        'autoSize'          : false,
        'titleShow'         : false,
        'autoScale'         : false,
        'transitionIn'      : 'none',
        'transitionOut'     : 'none',
        'scrolling'         : 'auto',
        'padding'           : 0,
        'margin'            : 0,                        
        'autoDimensions'    : false,
        'width'             : EM.Quickview.QS_FRM_WIDTH,
        'maxHeight'         : EM.Quickview.QS_FRM_HEIGHT,
        'centerOnScroll'    : true,            
        'height'            : 'auto',
        'ajaxLoad'          : null,
        'beforeLoad'        : function() {
            $("head").append('<style type="text/css" id="fancybox_hide_loading_css">#fancybox-loading{display:none}.fancybox-overlay{background:transparent}</style>');
            $(".loader-container").hide();
            $(this.element).parent().children(".loader-container").show();
        },
        'afterLoad'        : function() {     
            $("#fancybox_hide_loading_css").remove();
            $(".loader-container").hide();
        },
        'afterClose': function(){
            setTimeout(function(){
                $("#fancybox_hide_loading_css").remove();
            }, 500);
            $(".loader-container").hide();
        },
        'helpers': {
            overlay: {
                locked: false
            }
        }
    });
});


