<?php declare(strict_types=1);

namespace UCSC\Blocks\Assets;

class Assets_Subscriber {

	public function register(): void {
		add_action( 'wp_enqueue_scripts', static function (): void {
			( new Public_Assets_Enqueuer() )->register( Public_Assets_Enqueuer::ASSETS_FILE, Public_Assets_Enqueuer::PUBLIC );
		}, 10, 0 );

		add_action( 'enqueue_block_editor_assets', static function (): void {
			( new Editor_Assets_Enqueuer() )->register( Editor_Assets_Enqueuer::ASSETS_FILE, Editor_Assets_Enqueuer::EDITOR );
		}, 10, 0 );
	}

}
