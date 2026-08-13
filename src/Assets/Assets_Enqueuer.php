<?php
/**
 * Block asset enqueuing.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Assets;

/**
 * Enqueues the built CSS and JS for every registered UCSC block.
 *
 * Discovery is by registry scan rather than by iterating the registered
 * classes: any block whose name contains "ucsc-custom-functionality/" is
 * picked up. The last segment of that block name must therefore match the
 * build directory name, or the asset lookup silently finds nothing.
 */
class Assets_Enqueuer {

	use Assets;

	/**
	 * Absolute filesystem path to the built assets directory.
	 *
	 * @var string
	 */
	protected string $assets_path;

	/**
	 * Public URL of the built assets directory.
	 *
	 * @var string
	 */
	protected string $assets_path_uri;

	/**
	 * Resolve the built-assets directory as both a path and a URL.
	 *
	 * @param string $assets_folder Path to the built assets, relative to the plugin root.
	 */
	public function __construct( string $assets_folder = 'build/views/' ) {
		$this->assets_path     = trailingslashit( UCSC_DIR ) . $assets_folder;
		$this->assets_path_uri = trailingslashit( UCSC_PLUGIN_URL ) . $assets_folder;
	}

	/**
	 * Enqueue the stylesheet and script for every registered UCSC block.
	 *
	 * Version and dependencies come from each block's generated asset file.
	 *
	 * Note: the style handle is suffixed per block, but the script handle is
	 * not — every block enqueues its script under the same $handle_file, so
	 * only the first one registered actually loads. Tracked in #102; left
	 * as-is here so this stays a documentation-only change.
	 *
	 * @param string $assets_file Filename of the generated asset manifest, e.g. index.asset.php.
	 * @param string $handle_file Script handle, also used as the .js basename.
	 * @param string $css_handle  Stylesheet handle, also used as the .css basename.
	 *
	 * @return void
	 */
	public function register( string $assets_file, string $handle_file, string $css_handle ): void {
		$ucsc_custom_blocks = array_filter(
			\WP_Block_Type_Registry::get_instance()->get_all_registered(),
			static function ( $block ) {
				return stripos( $block->name, 'ucsc-custom-functionality/' ) !== false;
			}
		);

		if ( empty( $ucsc_custom_blocks ) ) {
			return;
		}

		foreach ( $ucsc_custom_blocks as $block ) {
			$parts      = explode( '/', $block->name );
			$block_name = end( $parts );

			$args = $this->get_asset_file_args( $this->assets_path . $block_name . '/' . $assets_file );

			wp_enqueue_style(
				$css_handle . '-' . $block_name,
				$this->assets_path_uri . $block_name . '/' . $css_handle . '.css',
				[],
				$args['version'] ?? false,
				'all',
			);

			wp_enqueue_script(
				$handle_file,
				$this->assets_path_uri . $block_name . '/' . $handle_file . '.js',
				$args['dependencies'] ?? [],
				$args['version'] ?? false,
				true,
			);
		}
	}
}
