var ajaxcart_timer;
var ajaxcart_sec;
jQuery.noConflict();
	function setAjaxData(data,iframe,type){
		if(data.status == 'ERROR'){
			alert(data.message.replace("<br/>",""));
		}else{
            if(jQuery('.header-container .mini-cart')){
                jQuery('.header-container .mini-cart').replaceWith(data.toplink);
            }
            if(jQuery('.fixed-header .mini-cart')){
                jQuery('.fixed-header .mini-cart').replaceWith(data.toplink);
            }
            if(jQuery('.sticky-header .mini-cart')){
                jQuery('.sticky-header .mini-cart').replaceWith(data.toplink);
            }
            if(jQuery('.col-right .block.block-cart')){
                jQuery('.col-right .block.block-cart').replaceWith(data.cart_sidebar);
            }
			jQuery('#after-loading-success-message #success-message-container .msg-box').html(data.message);
	        jQuery.fancybox.close();
			if(type!='item'){
				jQuery('#after-loading-success-message').fadeIn(200);
                ajaxcart_sec = jQuery('#after-loading-success-message .timer').text();
                ajaxcart_timer = setInterval(function(){
                    jQuery('#after-loading-success-message .timer').html(jQuery('#after-loading-success-message .timer').text()-1);
                },1000)
                setTimeout(function(){
                    jQuery('#after-loading-success-message').fadeOut(200);
                    clearTimeout(ajaxcart_timer);
                    setTimeout(function(){
                        jQuery('#after-loading-success-message .timer').html(ajaxcart_sec);
                    }, 1000);
                },ajaxcart_sec*1000);
			}
		}
	}
	function setLocationAjax(el,url,id,type){
        var qty = 1;
        if(jQuery("#qty_"+id).val()>0)
            qty = jQuery("#qty_"+id).val();
        if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url += 'isAjax/1';
		url = url.replace("checkout/cart","ajaxcart/index");
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
		jQuery('#loading-mask').show();
        jQuery(el).parent().parent().parent().children(".product-image-area").children(".loader-container").show();
        jQuery(el).parent().children(".loader-container").show();
		try {
			jQuery.ajax( {
				url : url,
				dataType : 'json',
                data: {qty: qty},
				success : function(data) {
					jQuery('#loading-mask').hide();
                    jQuery(".loader-container").hide();
         			setAjaxData(data,false,type);
				}
			});
		} catch (e) {
		}
	}

    function showOptions(id){
		initFancybox();
        jQuery('#fancybox'+id).trigger('click');
    }
	
	function initFancybox(){
		jQuery.noConflict();
		jQuery(document).ready(function(){
		jQuery('.fancybox').fancybox({
				hideOnContentClick : true,
				width: 382,
				autoDimensions: true,
				type : 'iframe',
				showTitle: false,
				scrolling: 'no',
				onComplete: function(){
					jQuery('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
						jQuery('#fancybox-content').height(jQuery(this).contents().find('body').height()+100);
						jQuery.fancybox.resize();
					});

				},
                'beforeLoad'        : function() {
                    jQuery("head").append('<style type="text/css" id="fancybox_hide_loading_css">#fancybox-loading{display:none}.fancybox-overlay{background:transparent}</style>');
                    jQuery(".loader-container").hide();
                    jQuery(this.element).parent().parent().parent().children(".product-image-area").children(".loader-container").show();
                    jQuery(this.element).parent().children(".loader-container").show();
                },
                'afterLoad'        : function() {     
                    jQuery("#fancybox_hide_loading_css").remove();
                    jQuery(".loader-container").hide();
                },
                'afterClose': function(){
                    setTimeout(function(){
                        jQuery("#fancybox_hide_loading_css").remove();
                    }, 500);
                    jQuery(".loader-container").hide();
                }
			}
		);
		});   	
	}
	function ajaxCompare(el,url,id){
	    url = url.replace("catalog/product_compare/add","ajaxcart/whishlist/compare");
		if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url += 'isAjax/1';
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
		jQuery('#loading-mask').show();
        jQuery(el).parent().parent().parent().children(".product-image-area").children(".loader-container").show();
        jQuery(el).parent().children(".loader-container").show();
	    jQuery.ajax( {
		    url : url,
		    dataType : 'json',
		    success : function(data) {
			    jQuery('#loading-mask').hide();
                jQuery(".loader-container").hide();
			    if(data.status == 'ERROR'){
				    alert(data.message.replace("<br/>",""));
			    }else{
				    if(jQuery('.block-compare').length){
                        jQuery('.block-compare').replaceWith(data.sidebar);
                    }else{
                        if(jQuery('.col-right').length){
                    	    jQuery('.col-right').prepend(data.sidebar);
                        }
                    }
                    if(jQuery('.compare-link').length){
                        jQuery('.compare-link').replaceWith(data.compare_popup);
                    }
                    alert(data.message.replace("<br/>",""));
			    }
		    }
	    });
    }
    function ajaxWishlist(el,url,id){
	    url = url.replace("wishlist/index","ajaxcart/whishlist");
        if (url.indexOf("?")){
            url = url.split("?")[0];
        }
		url += 'isAjax/1';
        if(window.location.href.match("https://") && !url.match("https://")){
            url = url.replace("http://", "https://");
        }
        if(window.location.href.match("http://") && !url.match("http://")){
            url = url.replace("https://", "http://");
        }
	    jQuery('#loading-mask').show();
        jQuery(el).parent().parent().parent().children(".product-image-area").children(".loader-container").show();
        jQuery(el).parent().children(".loader-container").show();
	    jQuery.ajax( {
		    url : url,
		    dataType : 'json',
		    success : function(data) {
			    jQuery('#loading-mask').hide();
                jQuery(".loader-container").hide();
			    if(data.status == 'ERROR'){
				    alert(data.message.replace("<br/>",""));
			    }else{
				    alert(data.message.replace("<br/>",""));
                    if(jQuery('.header > .quick-access > .links')){
                        jQuery('.header > .quick-access > .links').replaceWith(data.toplink);
                    }
			    }
		    }
	    });
    }
    function deleteAction(deleteUrl,itemId,msg){
	    var result =  confirm(msg);
	    if(result==true){
		    setLocationAjax(deleteUrl,itemId,'item')
	    }else{
		    return false;
	    }
    }