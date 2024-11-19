(function ($) {
	$( document ).ready( function () {
		if ( typeof acf === 'undefined' ) {
			return;
		}

		acf.add_filter('select2_ajax_data', function ( data, args, $input, field, instance ) {
			if ( ! data.field_key || ! data.field_key.includes( 'taxonomy_list_items' ) ) {
				return data;
			}

			// Add selected taxonomy type to request for the current block
			data.taxonomy_selected = acf.getField( $('div[data-name=taxonomies_list]') ).val();

			return data;

		});
	});

})( jQuery );
