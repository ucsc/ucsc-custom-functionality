<?php declare(strict_types=1);

namespace UCSC\Blocks\Template;

use WP_Block_Template;

class Post_Single extends Template {
	
	public const NAME    = 'ucsc_post_single_template';
	public const SLUG    = 'post-single';
	public const VERSION = '1.0';
	
	public function register( $query_result, $query, $template_type ) {
		$template = $this->register_template();
		
		if ( empty( $template ) || ! is_single() ) {
			return $query_result;
		}
		
		return $template;
	}

	protected function create_wp_block_template(): ?WP_Block_Template {
		$post_title   = esc_html__( 'UCSC Single Posts', 'ucsc' );
		$post_excerpt = esc_html__( 'Displays a single post with a heading, press coverage, categories, social sharing, and related stories.', 'ucsc' );
		$insert       = [
			'post_name'    => $this->get_slug(),
			'post_title'   => $post_title,
			'post_excerpt' => $post_excerpt,
			'post_type'    => 'wp_template',
			'post_status'  => 'publish',
			'post_content' => file_get_contents( UCSC_DIR . '/src/views/templates/post-single.html' ),
			'tax_input'    => [
				'wp_theme' => $this->get_namespace(),
			],
		];

		$id = wp_insert_post( $insert );

		if ( ! $id ) {
			return null;
		}
		
		return $this->hydrate_block_template_by_post( get_post( $id ) );
	}

}
