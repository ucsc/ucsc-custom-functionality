<?php
/**
 * Responsive image helper.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components\Traits;

/**
 * Builds srcset attributes from a remote image payload.
 *
 * Used for images that arrive over REST from the news site, where the local
 * attachment helpers are unavailable because there is no local attachment.
 */
trait With_Image_Size {

	/**
	 * Build a srcset string from an image's available sizes.
	 *
	 * Returns an empty string when the image or its sizes are missing, so the
	 * caller can emit the attribute unconditionally.
	 *
	 * @param array $image Image payload, expected to carry 'url' and 'sizes'.
	 *
	 * @return string The srcset value, or an empty string.
	 */
	public function build_srcset( array $image = [] ): string {
		if ( empty( $image ) ) {
			return '';
		}

		$urls  = [];
		$sizes = $image['sizes'] ?? [];

		if ( empty( $sizes ) ) {
			return '';
		}

		foreach ( $sizes as $size ) {
			$urls[] = $image['url'] . ' ' . $size['width'] . 'w ' . $size['height'] . 'h';
		}

		return implode( ', ', $urls );
	}
}
