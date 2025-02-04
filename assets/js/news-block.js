(function ($) {
	$( document ).ready( function () {
		if ( typeof acf === 'undefined' ) {
			return;
		}

		acf.add_filter('select2_ajax_data', function ( data, args, $input, field, instance ) {
			if ( ! data.field_key || data.field_key !== 'news_query_block_taxonomy_items' ) {
				return data;
			}

			// Add selected taxonomy type to request for the current block
			data.taxonomy_selected = acf.getField( 'news_query_block_taxonomies' ).val();

			return data;

		});
	});

})( jQuery );
