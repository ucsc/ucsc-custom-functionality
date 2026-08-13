<?php
/**
 * Shared asset-manifest helper.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Assets;

/**
 * Reads the asset manifests webpack generates alongside each built entry.
 */
trait Assets {

	/**
	 * Read a generated asset manifest.
	 *
	 * These files return an array with 'dependencies' and 'version' keys.
	 * A missing or malformed file yields an empty array rather than an error,
	 * so a block with no built assets degrades quietly.
	 *
	 * @param string $file_path Absolute path to the generated *.asset.php file.
	 *
	 * @return array The manifest contents, or an empty array.
	 */
	public function get_asset_file_args( string $file_path ): array {
		if ( ! file_exists( $file_path ) ) {
			return [];
		}
		$args = require $file_path;

		return is_array( $args ) ? $args : [];
	}
}
