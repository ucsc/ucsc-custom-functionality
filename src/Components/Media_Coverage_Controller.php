<?php
/**
 * Media coverage block controller.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Media_Coverage_Block;
use UCSC\Blocks\Components\Traits\With_Image_Size;
use UCSC\Blocks\Components\Traits\With_Primary_Term;

/**
 * Prepares entries for the Media Coverage block.
 *
 * Queries the media_coverage post type rather than posts. Each entry links
 * out to coverage on another site, so the source name and URL come from
 * per-post ACF fields rather than from the permalink.
 */
class Media_Coverage_Controller extends Query_Loop_Controller {

	use With_Image_Size;
	use With_Primary_Term;

	/**
	 * How many entries to display.
	 *
	 * @var int
	 */
	protected int $number_of_posts_display = 6;
	/**
	 * Queries media coverage entries rather than posts.
	 *
	 * @var string[]
	 */
	protected array $post_types = [ 'media_coverage' ];

	/**
	 * Read the query configuration and the all-coverage link.
	 *
	 * @param mixed $block The block instance supplied by the render callback.
	 */
	public function __construct( $block ) {
		parent::__construct( $block );

		$this->cta = (array) get_field( Media_Coverage_Block::CTA_FIELD ) ?: [];
	}

	/**
	 * The block's wrapper attributes.
	 *
	 * @return string
	 */
	public function get_attributes(): string {
		return wp_kses_data(
			get_block_wrapper_attributes(
				[
					'class' => implode(
						' ',
						[
							'ucsc-media-coverage-block',
							'alignfull',
							'has-lightest-gray-background-color',
							'has-global-padding',
							'is-layout-constrained',
						]
					),
				]
			)
		);
	}

	/**
	 * The block heading, or an empty string.
	 *
	 * @return string
	 */
	public function get_title(): string {
		$title = (string) get_field( Media_Coverage_Block::TITLE_FIELD );

		return strlen( $title ) < 1 ? '' : $title;
	}

	/**
	 * Whether a URL points at this site.
	 *
	 * Lets the view mark outbound coverage links differently.
	 *
	 * @param string $url URL to test.
	 *
	 * @return bool
	 */
	public function is_internal_url( string $url ): bool {
		$current_site = get_bloginfo( 'url' );

		return stripos( $url, $current_site ) !== false;
	}

	/**
	 * Shape each coverage entry into a card.
	 *
	 * @param array $posts         Post IDs to prepare.
	 * @param bool  $is_auto_query Whether the IDs came from the automatic query.
	 *
	 * @return array
	 */
	protected function prepare_posts_for_display( array $posts = [], bool $is_auto_query = false ): array {
		$items = [];

		foreach ( $posts as $post_id ) {
			if ( is_bool( $post_id ) || $post_id < 1 ) {
				continue;
			}
			$image_id   = get_post_thumbnail_id( $post_id );
			$image_meta = $image_id > 0 ? wp_get_attachment_metadata( $image_id ) : [];
			$image_url  = wp_get_attachment_image_url( $image_id, 'thumbnail' );
			$args       = [
				'id'           => $post_id,
				'title'        => get_the_title( $post_id ),
				'image'        => array_merge(
					[
						'id'  => $image_id,
						'url' => $image_url,
					],
					$image_meta
				),
				'source_title' => get_field( 'article_source', $post_id ),
				'source_url'   => get_field( 'article_url', $post_id ),
			];

			$items[] = $args;
		}

		return $items;
	}
}
