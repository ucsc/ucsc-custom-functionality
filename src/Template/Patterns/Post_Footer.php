<?php declare(strict_types=1);

namespace UCSC\Blocks\Template\Patterns;

class Post_Footer extends Pattern {

	
	public const SLUG = 'post-footer';

    public function register(): void {
        register_block_pattern( $this->build_pattern_name(), $this->get_args() );
    }

	public function get_args(): array {
		return [
			'title'       => esc_html__( 'Post Footer', 'ucsc' ),
			'filePath'    => UCSC_DIR . '/src/views/patterns/post-footer.php',
			'description' => esc_html__( 'Display post taxonomies, social links and related posts', 'ucsc' ),
			'categories'  => [
				'theme',
			],
		];
	}

}
