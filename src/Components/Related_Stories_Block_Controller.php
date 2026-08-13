<?php
/**
 * Related stories block controller.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Contracts\Taxonomies;
use UCSC\Blocks\Blocks\Query_Loop;
use UCSC\Blocks\Components\Traits\With_Image_Size;
use UCSC\Blocks\Components\Traits\With_Primary_Term;

/**
 * Prepares posts for the Related Stories block.
 */
class Related_Stories_Block_Controller extends Query_Loop_Controller {

	use With_Image_Size;
	use With_Primary_Term;

	/**
	 * How many posts to display.
	 *
	 * @var int
	 */
	protected int $number_of_posts_display = 3;

	/**
	 * The block's wrapper attributes, full-width and constrained.
	 *
	 * @return string
	 */
	public function get_attributes(): string {
		$classes = [
			'ucsc-related-stories-block',
			'alignfull',
			'is-layout-constrained',
			'has-global-padding',
		];

		return wp_kses_data(
			get_block_wrapper_attributes(
				[
					'class' => implode( ' ', $classes ),
				]
			)
		);
	}

	/**
	 * Shape each post into a card.
	 *
	 * Falls back to the category taxonomy when none was chosen.
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
			$image_url  = wp_get_attachment_url( $image_id );

			$taxonomy = 'category';
			if ( isset( $this->query_loop[ Query_Loop::QUERY_LOOP ][ Taxonomies::TAXONOMIES ] ) ) {
				$taxonomy = $this->query_loop[ Query_Loop::QUERY_LOOP ][ Taxonomies::TAXONOMIES ];
			}

			$category = $this->get_primary_term( $post_id, $taxonomy );
			$args     = [
				'id'       => $post_id,
				'title'    => get_the_title( $post_id ),
				'image'    => array_merge(
					[
						'id'  => $image_id,
						'url' => $image_url,
					],
					$image_meta !== false ? $image_meta : []
				),
				'category' => $category,
			];

			$items[] = $args;
		}

		return $items;
	}
}
