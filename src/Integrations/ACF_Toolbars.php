<?php
/**
 * ACF WYSIWYG toolbar definitions.
 *
 * @package ucsc
 */

namespace UCSC\Blocks\Integrations;

/**
 * Defines the cut-down WYSIWYG toolbars offered to editors.
 */
class ACF_Toolbars {

	/**
	 * Toolbar name, as selected on an ACF WYSIWYG field.
	 *
	 * @var string
	 */
	public const SIMPLE = 'ucsc_simple_toolbar';

	/**
	 * Add a minimal toolbar offering only bold, italic and link.
	 *
	 * Used for fields where richer formatting would break the block's layout.
	 *
	 * @param array $toolbars Toolbars registered so far.
	 *
	 * @return array The toolbars, with the simple one added.
	 */
	public function register_simple_toolbar( array $toolbars ): array {
		$toolbars[ self::SIMPLE ]    = [];
		$toolbars[ self::SIMPLE ][1] = [ 'bold', 'italic', 'link' ];

		return $toolbars;
	}
}
