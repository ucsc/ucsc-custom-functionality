<?php declare(strict_types=1);

namespace UCSC\Blocks;

use UCSC\Blocks\Assets\Assets_Subscriber;
use UCSC\Blocks\Blocks\Featured_Block;
use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Hooks\News_Blocks_Hooks;

class Core {
	
	public function init(): void {
		$this->blocks();
		$this->scripts();
	}
	
	protected function blocks(): void {
		add_action( 'init', function (): void {
			$this->init_blocks();
			( new News_Blocks_Hooks() )->hooks();
			( new Assets_Subscriber() )->register();
		}, 10, 0 );
	}
	
	protected function scripts(): void {
		add_action( 'admin_enqueue_scripts', static function (): void {
			wp_register_script(
				'ucsc-news-block-scripts',
				UCSC_PLUGIN_URL . '/assets/js/news-block.js',
				[],
				false,
				true
			);
			wp_enqueue_script( 'ucsc-news-block-scripts' );
		}, 10, 0 );
	}
	
	protected function init_blocks(): void {
		$blocks = [
			News_Block::class     => '/src/views/news_block',
			Featured_Block::class => '/src/views/featured_block',
		];
		
		foreach ( $blocks as $block_class => $block_path ) {
			register_block_type( UCSC_DIR . $block_path );
			( new $block_class )->init();
		}
	}

}
