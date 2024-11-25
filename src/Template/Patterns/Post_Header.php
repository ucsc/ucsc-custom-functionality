<?php declare(strict_types=1);

namespace UCSC\Blocks\Template\Patterns;

class Post_Header extends Pattern {

	
	public const SLUG = 'post-header';

    public function register(): void {
        register_block_pattern( $this->build_pattern_name(), $this->get_args() );
    }

	public function get_args(): array {
		return [
			'title'       => esc_html__( 'Post Header', 'ucsc' ),
			'filePath'    => UCSC_DIR . '/src/views/patterns/post-header.php',
			'description' => esc_html__( 'Display post Featured Image, Press Inquiries and other information', 'ucsc' ),
			'categories'  => [
				'theme',
			],
		];
	}

}
