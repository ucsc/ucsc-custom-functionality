<?php declare(strict_types=1);

namespace UCSC\Blocks\Template\Patterns;

class Social_Sharing extends Pattern {

	public const SLUG = 'social-sharing';

	public function register(): void {
		register_block_pattern( $this->build_pattern_name(), $this->get_args() );
	}

	public function get_args(): array {
		return [
			'title'       => esc_html__( 'Social Sharing', 'ucsc' ),
			'filePath'    => UCSC_DIR . '/src/views/patterns/social-sharing.php',
			'description' => esc_html__( 'Display social sharing links', 'ucsc' ),
			'categories'  => [
				'theme',
			],
		];
	}

}
