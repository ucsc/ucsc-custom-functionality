<?php declare(strict_types=1);

namespace UCSC\Blocks;

use UCSC\Blocks\Assets\Assets_Subscriber;
use UCSC\Blocks\Blocks\Featured_Block;
use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Hooks\News_Blocks_Hooks;
use UCSC\Blocks\Hooks\Taxonomies_Hooks;

class Core {

	public const BLOCKS_PUBLIC = [
		News_Block::class => '/src/views/news_block',
	];
	
	public const BLOCKS_NEWS_ONLY = [
		Featured_Block::class => '/src/views/featured_block',
	];
	
	public function init(): void {
		$this->blocks();
		$this->scripts();
	}
	
	protected function blocks(): void {
		add_action( 'init', function (): void {
			$this->init_blocks();
			( new News_Blocks_Hooks() )->hooks();
			( new Assets_Subscriber() )->register();

			if ( ! defined( 'UCSC_NEWS_SITE' ) || ! UCSC_NEWS_SITE ) {
				return;
			}

			( new Taxonomies_Hooks() )->hooks();
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

			if ( ! defined( 'UCSC_NEWS_SITE' ) && ! UCSC_NEWS_SITE ) {
				return;
			}

			wp_register_script(
				'ucsc-custom-block-scripts',
				UCSC_PLUGIN_URL . '/assets/js/custom-blocks.js',
				[],
				false,
				true
			);
			wp_enqueue_script( 'ucsc-custom-block-scripts' );
		}, 10, 0 );
	}
	
	protected function init_blocks(): void {
		foreach ( self::BLOCKS_PUBLIC as $block_class => $block_path ) {
			register_block_type( UCSC_DIR . $block_path );
			( new $block_class )->init();
		}
		
		if ( ! defined( 'UCSC_NEWS_SITE' ) && ! UCSC_NEWS_SITE ) {
			return;
		}

		foreach ( self::BLOCKS_NEWS_ONLY as $block_class => $block_path ) {
			register_block_type( UCSC_DIR . $block_path );
			( new $block_class )->init();
		}
	}

}
