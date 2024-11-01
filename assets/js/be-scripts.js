jQuery(document).ready( function($) {
	"use strict";

	var redirect_val = $('input[type=radio][name=wpmll_redirect_to]:checked').val();
	show_hide_fields(redirect_val)
	var allowed_emails_val = $('input[type=radio][name=wpmll_allowed_domains]:checked').val();
	show_hide_emails_field(allowed_emails_val)

	$(document).on('click', '.wpmll-add-allowed-domain', function (e) {
		e.preventDefault();
		var html = '<div class="wpmll-allowed-domains-list-item">@<input type="text" name="wpmll_allowed_domains_list[]" /><a href="#" class="wpmll-delete-allowed-domain"> Remove</a></div>';
		$(this).prev('.wpmll-allowed-domains-list').append(html);
	})

	$(document).on('click', '.wpmll-add-allowed-email', function (e) {
		e.preventDefault();
		var html = '<div class="wpmll-allowed-emails-list-item"><input type="email" name="wpmll_allowed_emails_list[]" /><a href="#" class="wpmll-delete-allowed-email">Remove</a></div>';
		$(this).prev('.wpmll-allowed-emails-list').append(html);
	})

	$(document).on('click', '.wpmll-delete-allowed-domain', function (e) {
		e.preventDefault();
		$(this).parent('.wpmll-allowed-domains-list-item').remove();
	})

	$(document).on('click', '.wpmll-delete-allowed-email', function (e) {
		e.preventDefault();
		$(this).parent('.wpmll-allowed-emails-list-item').remove();
	})

	$(document).on('blur', '#wpmll_link_validity', function (e) {
		e.preventDefault();
		$('#wpmll_link_validity_slider').val($(this).val())
	})

	$(document).on('change', 'input[type=radio][name=wpmll_redirect_to]', function (e) {
		e.preventDefault();
		show_hide_fields($(this).val())
	})

	$(document).on('change', 'input[type=radio][name=wpmll_allowed_domains]', function (e) {
		e.preventDefault();
		show_hide_emails_field($(this).val())
	})

	$(document).on('change', 'input[type=radio][name=wpmll_enabled]', function (e) {
		e.preventDefault();
		if (1 == $(this).val()) {
			$('.wpmll-enabled-form').addClass('is-enabled');
		} else {
			$('.wpmll-enabled-form').removeClass('is-enabled');
		}
	})

})

function show_hide_emails_field(val) {
	"use strict";
	var $ = jQuery;
	switch (val) {
		case 'emails':
			$('#wpmll-allowed-domains').hide();
			$('#wpmll-allowed-emails').show();
			break;
		case 'custom':
			$('#wpmll-allowed-domains').show();
			$('#wpmll-allowed-emails').hide();
			break;
		default:
			$('#wpmll-allowed-domains').hide();
			$('#wpmll-allowed-emails').hide();
			break;
	}
}
function show_hide_fields(val) {
	"use strict";
	var $ = jQuery;
	switch (val) {
		case 'url':
			$('#pxl-redirect-page').hide();
			$('#pxl-redirect-url').show();
			break;
		case 'page':
			$('#pxl-redirect-page').show();
			$('#pxl-redirect-url').hide();
			break;
		default:
			$('#pxl-redirect-page').hide();
			$('#pxl-redirect-url').hide();
			break;
	}
}

function outputUpdate( value ) {
	"use strict";
	var $ = jQuery;
	$('#wpmll_link_validity').val(value);
}
