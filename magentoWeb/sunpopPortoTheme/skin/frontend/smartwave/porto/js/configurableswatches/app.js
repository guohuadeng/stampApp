
// ==============================================
// PDP - image zoom - needs to be available outside document.ready scope
// ==============================================

var ProductMediaManager = {
    IMAGE_ZOOM_THRESHOLD: 20,
    imageWrapper: null,

    swapImage: function(targetImage) {
        targetImage = jQuery(targetImage);
        targetImage.addClass('gallery-image');

        if(targetImage[0].complete) { //image already loaded -- swap immediately
			 jQuery("li.etalage_thumb").trigger('zoom.destroy');
			 jQuery("li.etalage_thumb .gallery-image").remove();
			 jQuery("li.etalage_thumb img.etalage_thumb_image").removeClass("hide").show();
			 jQuery("li.etalage_thumb_active img.etalage_thumb_image").addClass("hide").hide();
			 jQuery(targetImage).insertBefore(jQuery("li.etalage_thumb_active img.etalage_thumb_image"));
			 imagesLoaded(targetImage, function() {
				 if(typeof zoom_enabled !== 'undefined' && zoom_enabled == true){
					 if(typeof zoom_type !== 'undefined' && zoom_type == 1){
						 jQuery("li.etalage_thumb").zoom({target: jQuery(".product-view-zoom-area"),touch:false});
					 } else {
						 jQuery("li.etalage_thumb").zoom({touch:false});
					 }
				 }
			 });

        } else { //need to wait for image to load
			 jQuery("li.etalage_thumb").trigger('zoom.destroy');
			 jQuery("li.etalage_thumb .gallery-image").remove();
			 jQuery("li.etalage_thumb img.etalage_thumb_image").removeClass("hide").show();
			 jQuery("li.etalage_thumb_active img.etalage_thumb_image").addClass("hide").hide();
			 jQuery(targetImage).insertBefore(jQuery("li.etalage_thumb_active img.etalage_thumb_image"));
            imagesLoaded(targetImage, function() {
				 if(typeof zoom_enabled !== 'undefined' && zoom_enabled == true){
					 if(typeof zoom_type !== 'undefined' && zoom_type == 1){
						 jQuery("li.etalage_thumb").zoom({target: jQuery(".product-view-zoom-area"),touch:false});
					 } else {
						 jQuery("li.etalage_thumb").zoom({touch:false});
					 }
				 }
            });

        }
		jQuery("li.etalage_thumb .gallery-image").each(function(){
			jQuery(this).attr("style",jQuery(this).parent().children(".etalage_thumb_image").attr("style"));
		});
        jQuery("li.etalage_thumb .gallery-image").show();
    },
    init: function() {
        ProductMediaManager.imageWrapper = jQuery('.product-img-box');

        jQuery(document).trigger('product-media-loaded', ProductMediaManager);
    }
};

jQuery(document).ready(function() {
    ProductMediaManager.init();
});