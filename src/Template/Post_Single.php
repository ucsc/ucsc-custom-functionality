<?php
/**
 * Single post block template.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Template;

use WP_Block_Template;

/**
 * Injects the custom single-post template.
 *
 * Applies to single posts only, and steps aside for post embeds, which
 * request their own template through the same filter.
 */
class Post_Single extends Template {

	/**
	 * Identifier for the template.
	 *
	 * @var string
	 */
	public const NAME = 'ucsc_post_single_template';
	/**
	 * Template slug.
	 *
	 * @var string
	 */
	public const SLUG = 'post-single';
	/**
	 * Template version.
	 *
	 * @var string
	 */
	public const VERSION = '1.0';

	/**
	 * Return this template when a single post is being viewed.
	 *
	 * Note: $query['slug__in'] is read unguarded, and callers frequently omit
	 * it. Tracked in #104.
	 *
	 * @param mixed  $query_result  Templates found so far.
	 * @param array  $query         The template query.
	 * @param string $template_type The template type being queried.
	 *
	 * @return mixed
	 */
	public function register( $query_result, $query, $template_type ) {
		$template = $this->register_template();

		if ( empty( $template ) || ! is_single() || in_array( 'embed-post', $query['slug__in'], true ) ) {
			return $query_result;
		}

		return $template;
	}

	/**
	 * Create the wp_template post from the bundled HTML.
	 *
	 * @return WP_Block_Template|null
	 */
	protected function create_wp_block_template(): ?WP_Block_Template {
		$post_title   = esc_html__( 'UCSC Single Posts', 'ucsc' );
		$post_excerpt = esc_html__( 'Displays a single post with a heading, press coverage, categories, social sharing, and related stories.', 'ucsc' );
		$insert       = [
			'post_name'    => $this->get_slug(),
			'post_title'   => $post_title,
			'post_excerpt' => $post_excerpt,
			'post_type'    => 'wp_template',
			'post_status'  => 'publish',
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a template shipped inside this plugin, not a remote or user-supplied file.
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
