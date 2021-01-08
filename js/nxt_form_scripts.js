(function($){
	// Implementation for the Ajax Custom Post Type Filter
	var url = window.location.href;
})(jQuery)

function nxt_form_shortcode_generator(){
	var filter = jQuery('#shortcode_generator_form');
	jQuery.ajax({
		url:filter.attr('action'),
		type:filter.attr('method'), // POST
		data:filter.serialize(), // form data
		success:function(data){
			jQuery('#shortcode_pastebin_container').html(data); // insert data
		}
	});
	return false;
}
function nxt_toggle_all_fields(){
	[...document.querySelectorAll('.nxt_field_element')].forEach((elem, index) => { 
		if(elem.getAttribute('checked') != 'checked') {
			elem.setAttribute('checked', 'checked');
		} else {
			elem.removeAttribute('checked');
		}
	});
}