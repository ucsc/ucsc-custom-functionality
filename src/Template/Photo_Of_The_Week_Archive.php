<?php declare(strict_types=1);

namespace UCSC\Blocks\Template;

use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;
use WP_Block_Template;

class Photo_Of_The_Week_Archive extends Template {

	public const NAME    = 'photo_of_the_week_archive';
	public const SLUG    = 'photo-of-the-week-archive';
	public const VERSION = '1.0.1';

	public function register( $query_result, $query, $template_type ) {
		$template = $this->register_template();

		if ( ! is_post_type_archive( Photo_Of_The_Week::NAME ) || empty( $template ) ) {
			return $query_result;
		}

		return $template;
	}

	protected function create_wp_block_template(): ?WP_Block_Template {
		$post_title   = esc_html__( 'Photo of the Week', 'ucsc' );
		$post_excerpt = esc_html__( 'Photo of the Week archive page', 'ucsc' );
		$insert       = [
			'post_name'    => $this->get_slug(),
			'post_title'   => $post_title,
			'post_excerpt' => $post_excerpt,
			'post_type'    => 'wp_template',
			'post_status'  => 'publish',
			'post_content' => file_get_contents( UCSC_DIR . '/src/views/templates/photo-of-the-week-archive.html' ),
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
