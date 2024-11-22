<?php declare(strict_types=1);

namespace UCSC\Blocks\Template\Patterns;

class Home_Link extends Pattern {

	
	public const SLUG = 'home-link';

	public function get_args(): array {
		return [
			'title'       => esc_html__( 'Home Link', 'ucsc' ),
			'filePath'    => UCSC_DIR . '/src/views/patterns/home-link.php',
			'description' => esc_html__( 'Display homepage link', 'ucsc' ),
			'categories'  => [
				'theme',
			],
		];
	}

}
