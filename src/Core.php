<?php declare(strict_types=1);

namespace UCSC\Blocks;

use UCSC\Blocks\Assets\Assets_Subscriber;
use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Hooks\News_Blocks_Hooks;

class Core {
	
	public function init(): void {
		$this->blocks();
		$this->scripts();
	}

    /**
     * @param array          $block      The block attributes.
     * @param string         $content    The block content.
     * @param bool           $is_preview Whether the block is being rendered for editing preview.
     * @param int            $post_id    The current post being edited or viewed.
     * @param \WP_Block|null $wp_block   The block instance (since WP 5.5).
     */
    public function render_template( array $block, string $content = '', bool $is_preview = false, int $post_id = 0, ?\WP_Block $wp_block = null ): void {
        $template = $block['render_template'];
        $path     = str_replace( 'build/views/', 'src/views/', $block['path'] );

        if ( ! file_exists( "$path/$template" ) ) {
            return;
        }

        include "$path/$template"; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.NotAbsolutePath
    }
	
	protected function blocks(): void {
		add_action( 'init', function (): void {
            $args = [
                'render_callback' => [ $this, 'render_template' ],
            ];
            register_block_type_from_metadata( trailingslashit( UCSC_DIR . '/build/views/news_block' ) . '/block.json', $args );
			( new News_Block() )->init();
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

}
