<?php
/**
 * Core block output filtering.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Template;

/**
 * Rewrites the output of core blocks used in the custom templates.
 *
 * These adjustments cannot be made in the templates themselves, because the
 * blocks being changed are core blocks whose markup the plugin does not
 * own. Each method returns the content untouched unless it recognises the
 * block, so the filter is safe to run against every block on the page.
 */
class Blocks_Render {

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
	 * Replace the core author name with the Co-Authors Plus byline.
	 *
	 * Falls through untouched when Co-Authors Plus is not active.
	 *
	 * @param string $block_content The rendered block.
	 * @param array  $block         The parsed block.
	 *
	 * @return string
	 */
	public function adjust_author_block( $block_content = '', $block = [] ) {
		if ( ! is_singular() || ! function_exists( 'coauthors_posts_links' ) || ! ( isset( $block['blockName'] ) && 'core/post-author-name' === $block['blockName'] ) ) {
			return $block_content;
		}

		return coauthors_posts_links(
			null,
			null,
			esc_html__( 'By ', 'ucsc' ),
			null,
			false
		);
	}

	/**
	 * Append the featured image's caption and credit.
	 *
	 * The credit is stored in the attachment's post content.
	 *
	 * @param string $block_content The rendered block.
	 * @param array  $block         The parsed block.
	 *
	 * @return string
	 */
	public function adjust_featured_image_block( $block_content = '', $block = [] ) {
		if ( ! is_singular() || ! ( isset( $block['blockName'] ) && 'core/post-featured-image' === $block['blockName'] ) ) {
			return $block_content;
		}

		$caption = get_the_post_thumbnail_caption() ?? '';
		if ( get_post_thumbnail_id() > 0 ) {
			$description = get_post( get_post_thumbnail_id() )->post_content ?? '';
		}

		if ( ! empty( $caption ) ) {
			$block_content = sprintf( '%s<p>%s</p>', $block_content, $caption );
		}

		if ( ! empty( $description ) ) {
			$block_content = sprintf( '%s<p>%s</p>', $block_content, $description );
		}

		return $block_content;
	}

	/**
	 * Wrap the post terms in a titled "Related Topics" section.
	 *
	 * @param string $block_content The rendered block.
	 * @param array  $block         The parsed block.
	 *
	 * @return string
	 */
	public function adjust_post_terms_block( $block_content = '', $block = [] ) {
		if ( ! is_single() || ! ( isset( $block['blockName'] ) && 'core/post-terms' === $block['blockName'] ) ) {
			return $block_content;
		}

		$block_content = sprintf(
			'<div class="ucsc-post-terms-wrapper"><h2>%s</h2>%s</div>',
			esc_html__( 'Related Topics', 'ucsc' ),
			$block_content
		);

		return $block_content;
	}

	/**
	 * Wrap the sharing links in a titled "Share" section.
	 *
	 * @param string $block_content The rendered block.
	 * @param array  $block         The parsed block.
	 *
	 * @return string
	 */
	public function adjust_social_share_block( $block_content = '', $block = [] ) {
		if ( ! is_singular() || ! ( isset( $block['blockName'] ) && 'outermost/social-sharing' === $block['blockName'] ) ) {
			return $block_content;
		}

		$block_content = sprintf(
			'<div class="ucsc-social-sharing-wrapper"><h2>%s</h2>%s</div>',
			esc_html__( 'Share', 'ucsc' ),
			$block_content
		);

		return $block_content;
	}
}
