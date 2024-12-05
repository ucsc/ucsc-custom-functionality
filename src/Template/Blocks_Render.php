<?php declare(strict_types=1);

namespace UCSC\Blocks\Template;

class Blocks_Render {

	private static self $instance;

	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function adjust_author_block( $block_content = '', $block = [] ) {
		if ( ! is_singular() || ! function_exists( 'coauthors_posts_links' ) || ! (  isset( $block['blockName'] ) && 'core/post-author-name' === $block['blockName'] ) ) {
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
	
	public function adjust_featured_image_block( $block_content = '', $block = [] ) {
		if ( ! is_singular() || ! (  isset( $block['blockName'] ) && 'core/post-featured-image' === $block['blockName'] ) ) {
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
	
	public function adjust_post_terms_block( $block_content = '', $block = [] ) {
		if ( ! is_singular() || ! (  isset( $block['blockName'] ) && 'core/post-terms' === $block['blockName'] ) ) {
			return $block_content;
		}

		$block_content = sprintf(
			'<div class="ucsc-post-terms-wrapper"><h2>%s</h2>%s</div>',
			esc_html__( 'Related Topics', 'ucsc' ),
			$block_content
		);

		return $block_content;
	}
	
	public function adjust_social_share_block( $block_content = '', $block = [] ) {
		if ( ! is_singular() || ! (  isset( $block['blockName'] ) && 'outermost/social-sharing' === $block['blockName'] ) ) {
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
