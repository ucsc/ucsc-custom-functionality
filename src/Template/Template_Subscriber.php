<?php
/**
 * Template hook registration.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Template;

/**
 * Wires up the template-related filters on news sites.
 *
 * Two jobs: routing every rendered block through Blocks_Render, and giving
 * new posts a starting block structure so editors begin from the intended
 * layout rather than an empty canvas.
 */
class Template_Subscriber {

	/**
	 * Register the block render filter and the default post template.
	 *
	 * The render filter runs late, at priority 100, so it sees output other
	 * filters have already produced.
	 *
	 * @return void
	 */
	public function init(): void {
		$block_renderer = Blocks_Render::instance();

		add_filter(
			'render_block',
			static function ( $block_content = '', $block = [] ) use ( $block_renderer ) {
				$block_content = $block_renderer->adjust_author_block( $block_content, $block );
				$block_content = $block_renderer->adjust_featured_image_block( $block_content, $block );
				$block_content = $block_renderer->adjust_post_terms_block( $block_content, $block );
				$block_content = $block_renderer->adjust_social_share_block( $block_content, $block );

				return $block_content;
			},
			100,
			2
		);

		add_action(
			'init',
			static function (): void {
				$post_type_object           = get_post_type_object( 'post' );
				$post_type_object->template = [
					[
						'ucsc-custom-functionality/post-header-block',
						[],
					],
					[
						'ucsc-custom-functionality/press-inquiries',
						[],
					],
					[
						'core/paragraph',
						[
							'content' => 'Replace this text with the content of your story. Be sure to fill out the "excerpt" in the Post area on the right.',
						],
					],
					[
						'core/post-terms',
						[
							'term' => 'category',
						],
					],
					[
						'outermost/social-sharing',
						[],
						[
							[
								'outermost/social-sharing-link',
								[
									'service' => 'facebook',
								],
							],
							[
								'outermost/social-sharing-link',
								[
									'service' => 'linkedin',
								],
							],
							[
								'outermost/social-sharing-link',
								[
									'service' => 'reddit',
								],
							],
							[
								'outermost/social-sharing-link',
								[
									'service' => 'print',
								],
							],
						],
					],
					[
						'ucsc-custom-functionality/related-stories-block',
						[],
					],
				];
			},
			10,
			0
		);
	}
}
