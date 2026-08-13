<?php
/**
 * Featured news block controller.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Contracts\Taxonomies;
use UCSC\Blocks\Blocks\Featured_News_Block;
use UCSC\Blocks\Blocks\Query_Loop;
use UCSC\Blocks\Components\Traits\With_Image_Size;
use UCSC\Blocks\Components\Traits\With_Primary_Term;

/**
 * Prepares posts for the Featured Stories block.
 *
 * The first card is rendered large and is the only one given an excerpt;
 * the rest are compact.
 */
class Featured_News_Block_Controller extends Query_Loop_Controller {

	use With_Image_Size;
	use With_Primary_Term;

	/**
	 * How many posts to display.
	 *
	 * @var int
	 */
	protected int $number_of_posts_display = 4;

	/**
	 * Read the query configuration and the all-news link.
	 *
	 * @param mixed $block The block instance supplied by the render callback.
	 */
	public function __construct( $block ) {
		parent::__construct( $block );

		$cta_field = get_field( Featured_News_Block::CTA_FIELD );
		$this->cta = is_array( $cta_field ) ? $cta_field : [];
	}

	/**
	 * Shape each post into a card.
	 *
	 * Skips invalid IDs, which manual mode can leave behind when a selected
	 * post is later deleted.
	 *
	 * @param array $posts         Post IDs to prepare.
	 * @param bool  $is_auto_query Whether the IDs came from the automatic query.
	 *
	 * @return array
	 */
	protected function prepare_posts_for_display( array $posts = [], bool $is_auto_query = false ): array {
		$items = [];

		foreach ( $posts as $key => $post_id ) {
			if ( is_bool( $post_id ) || $post_id < 1 ) {
				continue;
			}
			$image_id   = get_post_thumbnail_id( $post_id );
			$image_meta = $image_id > 0 ? wp_get_attachment_metadata( $image_id ) : [];
			$image_meta = is_array( $image_meta ) ? $image_meta : [];
			$image_url  = wp_get_attachment_url( $image_id );
			$selected   = $this->query_loop[ Query_Loop::QUERY_LOOP ][ Taxonomies::TAXONOMIES ] ?? '';
			$taxonomy   = ! empty( $selected ) ? $selected : 'category';
			$category   = $this->get_primary_term( $post_id, $taxonomy );
			$args       = [
				'id'       => $post_id,
				'title'    => get_the_title( $post_id ),
				'image'    => array_merge(
					[
						'id'  => $image_id,
						'url' => $image_url,
					],
					$image_meta
				),
				'category' => $category,
			];

			// Large card.
			if ( 0 === $key ) {
				$args['excerpt'] = get_the_excerpt( $post_id );
			}

			$items[] = $args;
		}

		return $items;
	}
}
