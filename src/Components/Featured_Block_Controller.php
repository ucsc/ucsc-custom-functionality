<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Featured_Block;
use UCSC\Blocks\Blocks\Query_Loop;
use UCSC\Blocks\Components\Traits\With_Image_Size;

class Featured_Block_Controller {
    
    use With_Image_Size;

	protected array $block;
	protected array $query_loop;
	protected int $number_of_posts_display = 4;
	
	public function __construct( $block ) {
		$this->block      = (array) $block;
        $this->query_loop = (array) get_field( Query_Loop::QUERY_LOOP );
	}

	public function get_items(): array {
		$query_type = $this->query_loop[ Query_Loop::QUERY_TYPE ];

		if ( empty( $query_type ) || $query_type === Query_Loop::AUTOMATIC ) {
			return $this->get_automatic_query_items();
		}
		
		return $this->get_manual_query_items();
	}
	
	protected function get_automatic_query_items(): array {
		$category_id = $this->query_loop[ Query_Loop::QUERY_LOOP ][ Query_Loop::TAX_ITEMS ];
		
        if ( (int) $category_id < 1 ) {
			return [];
		}
        
        if ( is_array( $category_id ) ) {
            $category_id = reset( $category_id );
        }
		
		$posts = get_posts( [
            'fields'      => 'ids',
			'post_type'   => 'post',
			'post_status' => 'published',
			'numberposts' => $this->number_of_posts_display,
			'tax_query'   => [
				[
					'taxonomy' => 'category',
					'terms'    => $category_id,
				],
			],
		] );
		
		if ( empty( $posts ) ) {
			return [];
		}
		
		return $this->prepare_posts_for_display( $posts );
	}
	
	protected function get_manual_query_items(): array {
		$posts = $this->query_loop[ Query_Loop::MANUAL_CARDS ];
        
		if ( empty( $posts ) ) {
			return [];
		}
        
        $posts = array_column( $posts, 'manual_card' );
		
		return $this->prepare_posts_for_display( $posts );
	}
	
	protected function prepare_posts_for_display( array $posts = [] ): array {
		$items = [];
		
		foreach ( $posts as $key => $post_id ) {
            if ( is_bool( $post_id ) || $post_id < 1 ) {
                continue;
            }
            $image_id  = get_post_thumbnail_id( $post_id );
			$image_meta = $image_id > 0 ? wp_get_attachment_metadata( $image_id ) : [];
            $image_url  = wp_get_attachment_url( $image_id );
			$category   = get_the_category( $post_id );
			$args       = [
                'id'       => $post_id,
				'title'    => get_the_title( $post_id ),
				'image'    => array_merge( [ 'id' => $image_id, 'url' => $image_url ], $image_meta ),
				'category' => ! empty( $category ) ? $category[0] : null,
			];

			// Large card
			if ( $key === 0 ) {
				$args['excerpt'] = get_the_excerpt( $post_id );
			}

			$items[] = $args;
		}

		return $items;
	}
	
}
