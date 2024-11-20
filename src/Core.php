<?php declare(strict_types=1);

namespace UCSC\Blocks;

use UCSC\Blocks\Assets\Assets_Subscriber;
use UCSC\Blocks\Blocks\Featured_Block;
use UCSC\Blocks\Blocks\Magazine_Block;
use UCSC\Blocks\Blocks\Media_Coverage_Block;
use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Hooks\News_Blocks_Hooks;
use UCSC\Blocks\Hooks\Taxonomies_Hooks;
use UCSC\Blocks\Integrations\Integrations_Subscriber;
use UCSC\Blocks\Object_Meta\Object_Meta_Definer;
use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;
use UCSC\Blocks\Query\Query_Subscriber;

class Core {

	public const BLOCKS_PUBLIC = [
		News_Block::class => '/src/views/news_block',
	];
	
	public const BLOCKS_NEWS_ONLY = [
		Featured_Block::class       => '/src/views/featured_block',
		Media_Coverage_Block::class => '/src/views/media_coverage_block',
		Magazine_Block::class       => '/src/views/magazine_block',
		'photos_week_loop'          => '/src/views/photos_week_loop',
	];
	
	public function init(): void {
		$this->blocks();
		$this->scripts();
		$this->post_types();
		$this->object_meta();
		$this->subscribers();
	}
	
	protected function subscribers(): void {
		if ( ! $this->is_news_site() ) {
			return;
		}
		( new Query_Subscriber() )->init();
		( new Integrations_Subscriber() )->init();
	}
	
	protected function object_meta(): void {
		if ( ! $this->is_news_site() ) {
			return;
		}

		( new Object_Meta_Definer() )->register();
	}
	
	protected function post_types(): void {
		if ( ! $this->is_news_site() ) {
			return;
		}

		add_action( 'init', static function (): void {
			( new Photo_Of_The_Week() )->register();
		}, 10, 0 );
	}
	
	protected function blocks(): void {
		add_action( 'init', function (): void {
			$this->init_blocks();
			( new News_Blocks_Hooks() )->hooks();
			( new Assets_Subscriber() )->register();

			if ( ! $this->is_news_site() ) {
				return;
			}

			( new Taxonomies_Hooks() )->hooks();
		}, 10, 0 );
	}
	
	protected function scripts(): void {
		add_action( 'admin_enqueue_scripts', function (): void {
			wp_register_script(
				'ucsc-news-block-scripts',
				UCSC_PLUGIN_URL . '/assets/js/news-block.js',
				[],
				false,
				true
			);
			wp_enqueue_script( 'ucsc-news-block-scripts' );

			if ( ! $this->is_news_site() ) {
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
		
		if ( ! $this->is_news_site() ) {
			return;
		}

		foreach ( self::BLOCKS_NEWS_ONLY as $block_class => $block_path ) {
			register_block_type( UCSC_DIR . $block_path );
			
			if ( ! class_exists( $block_class ) ) {
				continue;
			}
			( new $block_class )->init();
		}
	}
	
	private function is_news_site(): bool {
		return defined( 'UCSC_NEWS_SITE' ) && UCSC_NEWS_SITE;
	}

}
