/**
 *  Scroll To Top
 *
 * @package Astra Addon
 * @since  1.0.0
 */

(function ($) {
	
	jQuery(document).on('click', '.switch-to-astra-update-now', function(event) {

		var confirm = window.confirm( switchToAstra.confirm_message );
		if( ! confirm ) {
			event.preventDefault();
		}
	});

	jQuery(document).on('click', '#switch-to-astra-notice .notice-dismiss', function(event) {

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'switch_to_astra_updated',
			},
			success: function(data) {
				$( '#switch-to-astra-notice' ).slideUp('400');
			}
		});
	});
})(jQuery);
