jQuery(function($){
	$(window).scroll(function(){
		var scroll_left = - $(this).scrollLeft();
		$(".header-container.sticky-header .header-wrapper").css("cssText", "left: "+ scroll_left + "px !important;");
	});
});