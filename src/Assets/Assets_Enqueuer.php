<?php declare(strict_types=1);

namespace UCSC\Blocks\Assets;

class Assets_Enqueuer {

	use Assets;

	protected string $assets_path;
	protected string $assets_path_uri;

	public function __construct( string $assets_folder = 'build/views/' ) {
		$this->assets_path     = trailingslashit( UCSC_DIR ) . $assets_folder;
		$this->assets_path_uri = trailingslashit( UCSC_PLUGIN_URL ) . $assets_folder;
	}

	public function register( string $assets_file, string $handle_file ): void {
		$ucsc_custom_blocks = array_filter( \WP_Block_Type_Registry::get_instance()->get_all_registered(), static function ( $block ) {
			return stripos( $block->name, 'ucsc-custom-functionality/' ) !== false;
		} );

		if ( empty( $ucsc_custom_blocks ) ) {
			return;
		}

		foreach ( $ucsc_custom_blocks as $block ) {
			$path       = explode( '/', $block->path );
			$block_name = end( $path );

			$args = $this->get_asset_file_args( $this->assets_path . $block_name . '/' .$assets_file );

			wp_enqueue_style(
				$handle_file,
				$this->assets_path_uri . $block_name . '/' . $handle_file . '.css',
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
