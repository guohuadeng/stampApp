var page_loaded = false;
function reloadData(a) {
	jQuery('#fullajax_loader').show();
	a = a.replace("https://","//");
	a = a.replace("http://","//");
	jQuery.ajax({
		url: a,
        data:{fullpageajax:true},
		success: showResponse,
		error: function () {
			window.location.href = a
		}
	});
	history.pushState('', 'New Page Title New URL: '+a, a);
}
function showResponse(a) {
    window.scrollTo(0,0);
	var b = document.createElement('div');
	b.innerHTML = a;
	var c = b.innerHTML;
	var d = c.substr(0, c.indexOf('<div class="wrapper">'));
	var e = jQuery(jQuery(b).find('.wrapper').html());
	var a = a.replace('<body', '<body><div id="body"').replace('</body>', '</div></body>');
	var f = jQuery(a).filter("#body").attr("class");
	f = f.trim();
	jQuery(b).remove();
    if (d) {
        jQuery("head").html(d)
    }
	jQuery("body").attr("class", f);
	jQuery(".wrapper").html(e);
	jQuery('#fullajax_loader').hide()
}
function setLocation(b){
    myhost = new RegExp(location.host);
    reloadData(b)
}