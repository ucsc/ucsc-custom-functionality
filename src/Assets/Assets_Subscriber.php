<?php declare(strict_types=1);

namespace UCSC\Blocks\Assets;

class Assets_Subscriber {

	public function register(): void {
		add_action( 'wp_enqueue_scripts', static function (): void {
			( new Public_Assets_Enqueuer() )->register( Public_Assets_Enqueuer::ASSETS_FILE, Public_Assets_Enqueuer::PUBLIC, Public_Assets_Enqueuer::PUBLIC_CSS );
		}, 10, 0 );

		add_action( 'admin_enqueue_scripts', static function (): void {
			( new Public_Assets_Enqueuer() )->register( Public_Assets_Enqueuer::ASSETS_FILE, Public_Assets_Enqueuer::PUBLIC, Public_Assets_Enqueuer::PUBLIC_CSS );
		}, 10, 0 );

		add_action( 'enqueue_block_editor_assets', static function (): void {
			( new Editor_Assets_Enqueuer() )->register( Editor_Assets_Enqueuer::ASSETS_FILE, Editor_Assets_Enqueuer::EDITOR, Editor_Assets_Enqueuer::EDITOR );
		}, 10, 0 );

		wp_enqueue_block_style( 'core/post-terms', [
			'handle' => 'ucsc-custom-functionality-post-terms',
			'src'    => trailingslashit( UCSC_PLUGIN_URL ) . 'build/css/post-terms.css',
		]);

		wp_enqueue_block_style( 'outermost/social-sharing', [
			'handle' => 'ucsc-custom-functionality-social-sharing',
			'src'    => trailingslashit( UCSC_PLUGIN_URL ) . 'build/css/social-sharing.css',
		]);
	}

}
