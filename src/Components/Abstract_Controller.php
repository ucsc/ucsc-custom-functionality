<?php
/**
 * Block controller base class.
 *
 * @package ucsc
 */

namespace UCSC\Blocks\Components;

/**
 * Shared behaviour for block controllers.
 *
 * A controller sits between an ACF field group and its view: it reads the
 * saved field values into typed properties, does whatever querying or shaping
 * the view needs, and exposes the result through accessors. Views should
 * contain no query logic of their own.
 *
 * Despite the name this class is not abstract and is instantiated directly by
 * some views.
 */
class Abstract_Controller {

	/**
	 * Build the block's outer wrapper attributes.
	 *
	 * Delegates to get_block_wrapper_attributes() so that alignment, custom
	 * classes and other editor-set supports survive into the rendered markup.
	 *
	 * @param array $classes Extra classes to add to the wrapper.
	 *
	 * @return string The escaped attribute string.
	 */
	public function get_attributes( $classes = [] ): string {

		return wp_kses_data(
			get_block_wrapper_attributes(
				[
					'class' => implode( ' ', $classes ),
				]
			)
		);
	}
}
