(function ($) {
	$( document ).ready( function () {
		if ( typeof acf === 'undefined' ) {
			return;
		}

		// Use acf.addFilter, not the legacy acf.add_filter: the legacy wrapper
		// converts ACF field objects into their jQuery elements before calling
		// the callback, so `field.$el` would be undefined.
		acf.addFilter('select2_ajax_data', function ( data, args, $input, field, instance ) {
			if ( ! data.field_key || data.field_key !== 'news_query_block_taxonomy_items' ) {
				return data;
			}

			// Resolve this field's wrapper element from whatever ACF handed us.
			var $self;
			if ( field && field.$el ) {
				$self = field.$el;
			} else if ( field && field.jquery ) {
				$self = field;
			} else if ( $input && $input.length ) {
				$self = $input.closest( '.acf-field' );
			} else {
				return data;
			}

			// Add the taxonomy chosen in the *same* block to the request. Both
			// fields sit at the top level of the block's field group, so the
			// taxonomy field is a sibling; a page-wide lookup would return the
			// first News block on the page instead.
			var $taxonomy = $self.siblings( '.acf-field[data-key="news_query_block_taxonomies"]' ).first();
			var taxonomy  = $taxonomy.length ? acf.getField( $taxonomy ).val() : '';

			data.taxonomy_selected = taxonomy || '';

			return data;
		});

		fetchIndicator();
	});

	/**
	 * Show a "Loading stories" state on the News block preview while ACF
	 * re-renders it.
	 *
	 * ACF re-fetches a block's preview whenever a field in its sidebar form
	 * changes, but keeps showing the old preview until the new HTML arrives.
	 * Because the render calls news.ucsc.edu several times, that wait can be
	 * a few seconds with no feedback. This mirrors ACF's own change detection
	 * (input/change on the block form's inputs) to add a class to the preview
	 * wrapper in the canvas, and removes it once ACF reports the new render.
	 */
	function fetchIndicator() {
		var BLOCK_TYPE     = 'ucsc-custom-functionality/news-block';
		var FETCHING_CLASS = 'is-fetching-stories';
		var SAFETY_MS      = 30000;
		var timers         = {};

		// The editor canvas is usually an iframe; the preview lives inside it.
		function canvasDocument() {
			var iframe = document.querySelector( 'iframe[name="editor-canvas"]' );

			return iframe && iframe.contentDocument ? iframe.contentDocument : document;
		}

		function preview( clientId ) {
			var $preview = $( canvasDocument() ).find( '.acf-block-preview[data-block="' + clientId + '"]' );

			return $preview.data( 'type' ) === BLOCK_TYPE ? $preview : $();
		}

		function start( clientId ) {
			var $preview = preview( clientId );

			if ( ! $preview.length ) {
				return;
			}

			$preview.addClass( FETCHING_CLASS );

			// If the render never reports back (e.g. it errored), do not leave
			// the block dimmed forever.
			clearTimeout( timers[ clientId ] );
			timers[ clientId ] = setTimeout( function () {
				stop( clientId );
			}, SAFETY_MS );
		}

		function stop( clientId ) {
			clearTimeout( timers[ clientId ] );
			delete timers[ clientId ];
			preview( clientId ).removeClass( FETCHING_CLASS );
		}

		// ACF's block form carries data-block-id="block_<clientId>".
		function onFieldChange( event ) {
			var blockId = $( event.target ).closest( '.acf-block-fields' ).data( 'blockId' );

			if ( typeof blockId === 'string' && blockId.indexOf( 'block_' ) === 0 ) {
				start( blockId.substring( 6 ) );
			}
		}

		$( document )
			.on( 'input change', '.acf-block-fields input, .acf-block-fields textarea', onFieldChange )
			.on( 'change', '.acf-block-fields select', onFieldChange );

		acf.addAction( 'render_block_preview', function ( $el, attributes ) {
			if ( attributes && attributes.name === BLOCK_TYPE && attributes.id ) {
				stop( attributes.id.replace( /^block_/, '' ) );
			}
		} );
	}

})( jQuery );
