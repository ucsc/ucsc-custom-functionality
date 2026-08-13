<?php
/**
 * Plugin bootstrap.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks;

use UCSC\Blocks\Assets\Assets_Subscriber;
use UCSC\Blocks\Blocks\Featured_News_Block;
use UCSC\Blocks\Blocks\Magazine_Block;
use UCSC\Blocks\Blocks\Media_Coverage_Block;
use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Blocks\Photo_Of_The_Week_Block;
use UCSC\Blocks\Blocks\Post_Header_Block;
use UCSC\Blocks\Blocks\Press_Inquiries_Block;
use UCSC\Blocks\Blocks\Related_Stories_Block;
use UCSC\Blocks\Hooks\News_Blocks_Hooks;
use UCSC\Blocks\Hooks\Taxonomies_Hooks;
use UCSC\Blocks\Integrations\Integrations_Subscriber;
use UCSC\Blocks\Object_Meta\Object_Meta_Definer;
use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;
use UCSC\Blocks\Query\Query_Subscriber;
use UCSC\Blocks\Template\Photo_Of_The_Week_Archive;
use UCSC\Blocks\Template\Post_Single;
use UCSC\Blocks\Template\Template_Subscriber;

/**
 * Singleton bootstrap and block registry.
 *
 * Instantiated from plugin.php on plugins_loaded (priority 100), and only when
 * ACF PRO is active. Nothing in src/ runs without it.
 *
 * Registration is split in two: BLOCKS_PUBLIC loads everywhere, while
 * BLOCKS_NEWS_ONLY, the custom post type, templates and subscribers load only
 * when UCSC_NEWS_SITE is defined true.
 */
class Core {

	/**
	 * Registry key for the Photos of the Week query loop.
	 *
	 * Unlike its siblings this is a plain string rather than a class constant,
	 * because the block has no field-group class to instantiate.
	 *
	 * @var string
	 */
	public const PHOTOS_LOOP = 'photos-week-loop';

	/**
	 * Blocks registered on every site.
	 *
	 * Maps a field-group class to its build directory. The path must match the
	 * src/views/ directory name, since webpack mirrors the source directory
	 * rather than the block.json slug. A mismatch silently skips registration.
	 *
	 * @var array<string, string>
	 */
	public const BLOCKS_PUBLIC = [
		News_Block::class => '/build/views/news-block',
	];

	/**
	 * Blocks registered only when UCSC_NEWS_SITE is true.
	 *
	 * Same path contract as BLOCKS_PUBLIC.
	 *
	 * @var array<string, string>
	 */
	public const BLOCKS_NEWS_ONLY = [
		self::PHOTOS_LOOP              => '/build/views/photos-week-loop',
		Photo_Of_The_Week_Block::class => '/build/views/photo-of-the-week-block',
		Featured_News_Block::class     => '/build/views/featured-news-block',
		Media_Coverage_Block::class    => '/build/views/media-coverage-block',
		Magazine_Block::class          => '/build/views/magazine-block',
		Related_Stories_Block::class   => '/build/views/related-stories-block',
		Press_Inquiries_Block::class   => '/build/views/press-inquiries',
		Post_Header_Block::class       => '/build/views/post-header-block',
	];

	/**
	 * The single instance.
	 *
	 * @var self
	 */
	private static self $instance;

	/**
	 * Get the singleton instance, creating it on first call.
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register everything the plugin provides.
	 *
	 * Each step hooks its own WordPress action, so ordering here reflects
	 * grouping rather than execution order.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->blocks();
		$this->scripts();
		$this->post_types();
		$this->object_meta();
		$this->subscribers();
		$this->templates();
	}

	// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter -- signature fixed by the block render_callback contract.
	/**
	 * Render an ACF block by including its view template.
	 *
	 * Registered as the render_callback for every block. The stored path points
	 * into build/, but the PHP template is read from src/ so that view edits
	 * take effect without a rebuild; the index.php copied into build/ is never
	 * executed.
	 *
	 * @param array          $block      The block attributes.
	 * @param string         $content    The block content.
	 * @param bool           $is_preview Whether the block is being rendered for editing preview.
	 * @param int            $post_id    The current post being edited or viewed.
	 * @param \WP_Block|null $wp_block   The block instance (since WP 5.5).
	 *
	 * @return void
	 */
	public function render_template( array $block, string $content = '', bool $is_preview = false, int $post_id = 0, ?\WP_Block $wp_block = null ): void {
		$template = $block['render_template'];
		$path     = str_replace( 'build/views/', 'src/views/', $block['path'] );

		if ( ! file_exists( "$path/$template" ) ) {
			return;
		}

		include "$path/$template"; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
	}
	// phpcs:enable Generic.CodeAnalysis.UnusedFunctionParameter

	/**
	 * Inject the custom block templates on news sites.
	 *
	 * Deferred to after_setup_theme because the templates are tied to a
	 * wp_theme term and need the active theme resolved first.
	 *
	 * @return void
	 */
	protected function templates(): void {
		if ( ! $this->is_news_site() ) {
			return;
		}

		$templates = [
			Photo_Of_The_Week_Archive::class,
			Post_Single::class,
		];

		add_action(
			'after_setup_theme',
			static function () use ( $templates ): void {
				foreach ( $templates as $template ) {
					( new $template() )->init();
				}
			},
			10,
			0
		);
	}

	/**
	 * Register the news-site event subscribers.
	 *
	 * Query modifications, third-party integrations and core-block output
	 * filtering. All are news-only.
	 *
	 * @return void
	 */
	protected function subscribers(): void {
		if ( ! $this->is_news_site() ) {
			return;
		}
		( new Query_Subscriber() )->init();
		( new Integrations_Subscriber() )->init();
		( new Template_Subscriber() )->init();
	}

	/**
	 * Register the custom object meta fields. News sites only.
	 *
	 * @return void
	 */
	protected function object_meta(): void {
		if ( ! $this->is_news_site() ) {
			return;
		}

		( new Object_Meta_Definer() )->register();
	}

	/**
	 * Register the Photo of the Week post type. News sites only.
	 *
	 * @return void
	 */
	protected function post_types(): void {
		if ( ! $this->is_news_site() ) {
			return;
		}

		add_action(
			'init',
			static function (): void {
				( new Photo_Of_The_Week() )->register();
			},
			10,
			0
		);
	}

	/**
	 * Register blocks and their editor hooks on init.
	 *
	 * The News block and its hooks register everywhere; the taxonomy hooks are
	 * news-only.
	 *
	 * @return void
	 */
	protected function blocks(): void {
		add_action(
			'init',
			function (): void {
				$this->init_blocks();
				( new News_Blocks_Hooks() )->hooks();
				( new Assets_Subscriber() )->register();

				if ( ! $this->is_news_site() ) {
					return;
				}

				( new Taxonomies_Hooks() )->hooks();
			},
			10,
			0
		);
	}

	/**
	 * Enqueue the editor-side helper scripts.
	 *
	 * The News block script loads on every site; the custom-blocks script is
	 * news-only.
	 *
	 * @return void
	 */
	protected function scripts(): void {
		add_action(
			'admin_enqueue_scripts',
			function (): void {
				wp_register_script(
					'ucsc-news-block-scripts',
					UCSC_PLUGIN_URL . '/assets/js/news-block.js',
					[],
					UCSC_VERSION,
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
					UCSC_VERSION,
					true
				);
				wp_enqueue_script( 'ucsc-custom-block-scripts' );
			},
			10,
			0
		);
	}

	/**
	 * Register each block from its build-directory block.json.
	 *
	 * Every block shares one render_callback; the template is resolved per
	 * block from the metadata. Field-group classes are instantiated after
	 * registration so their ACF fields attach to the registered block name.
	 *
	 * @return void
	 */
	protected function init_blocks(): void {
		$args = [
			'render_callback' => [ $this, 'render_template' ],
		];

		foreach ( self::BLOCKS_PUBLIC as $block_class => $block_path ) {
			register_block_type_from_metadata( trailingslashit( UCSC_DIR . $block_path ) . '/block.json', $args );
			( new $block_class() )->init();
		}

		if ( ! $this->is_news_site() ) {
			return;
		}

		foreach ( self::BLOCKS_NEWS_ONLY as $block_class => $block_path ) {
			register_block_type_from_metadata( trailingslashit( UCSC_DIR . $block_path ) . '/block.json', $args );
			if ( ! class_exists( $block_class ) ) {
				continue;
			}
			( new $block_class() )->init();
		}
	}

	/**
	 * Whether this install is the news site.
	 *
	 * Single gate for every news-only feature.
	 *
	 * @return bool
	 */
	private function is_news_site(): bool {
		return defined( 'UCSC_NEWS_SITE' ) && UCSC_NEWS_SITE;
	}
}
