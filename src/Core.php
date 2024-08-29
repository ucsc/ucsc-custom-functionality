<?php declare(strict_types=1);

namespace UCSC\Blocks;

use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Hooks\News_Blocks_Hooks;

class Core {
	
	public function init(): void {
		$this->blocks();
		$this->scripts();
	}
	
	protected function blocks(): void {
		add_action( 'init', static function (): void {
			register_block_type( UCSC_DIR . '/src/views/news_block' );
			( new News_Block() )->init();
			( new News_Blocks_Hooks() )->hooks();
		}, 10, 0 );
	}
	
	protected function scripts(): void {
		add_action( 'admin_enqueue_scripts', static function (): void {
			wp_register_script(
				'ucsc-news-block-scripts',
				plugin_dir_url( dirname( __FILE__ ) . '/..' ) . '/assets/js/news-block.js',
				[],
				false,
				true
			);
			wp_enqueue_script( 'ucsc-news-block-scripts' );
		}, 10, 0 );
	}

}
