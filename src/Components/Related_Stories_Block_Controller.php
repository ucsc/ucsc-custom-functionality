<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Contracts\Taxonomies;
use UCSC\Blocks\Blocks\Query_Loop;
use UCSC\Blocks\Components\Traits\With_Image_Size;
use UCSC\Blocks\Components\Traits\With_Primary_Term;

class Related_Stories_Block_Controller extends Query_Loop_Controller {
	
	use With_Image_Size;
	use With_Primary_Term;

	protected int $number_of_posts_display = 3;

	public function get_attributes(): string {
		$classes = [
			'ucsc-related-stories-block',
			'alignfull',
			'is-layout-constrained',
			'has-global-padding',
		];

		return wp_kses_data( get_block_wrapper_attributes([
			'class' => implode( ' ', $classes ),
		]) );
	}
	
	protected function prepare_posts_for_display( array $posts = [], bool $is_auto_query = false ): array {
		$items = [];

		foreach ( $posts as $post_id ) {
			if ( is_bool( $post_id ) || $post_id < 1 ) {
				continue;
			}
			$image_id   = get_post_thumbnail_id( $post_id );
			$image_meta = $image_id > 0 ? wp_get_attachment_metadata( $image_id ) : [];
			$image_url  = wp_get_attachment_url( $image_id );
            $taxonomy   =  $this->query_loop[ Query_Loop::QUERY_LOOP ][ Taxonomies::TAXONOMIES ] ?: 'category';
			$category   = $this->get_primary_term( $post_id, $taxonomy );
			$args       = [
				'id'       => $post_id,
				'title'    => get_the_title( $post_id ),
				'image'    => array_merge( [ 'id' => $image_id, 'url' => $image_url ], $image_meta ),
				'category' => $category,
			];

			$items[] = $args;
		}

		return $items;
	}
	
}
