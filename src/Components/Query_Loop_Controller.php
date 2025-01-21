<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Query_Loop;
use UCSC\Blocks\Components\Traits\With_CTA;

abstract class Query_Loop_Controller {
	
	use With_CTA;

	protected array $block;
	protected array $cta;
	protected array $query_loop;
	protected bool $exclude_current_post_from_query = true;

	protected int $number_of_posts_display = 4;
	protected array $post_types            = [ 'post', ];

	abstract protected function prepare_posts_for_display( array $posts = [], bool $is_auto_query = false ): array;

	public function __construct( $block ) {
		$this->block      = (array) $block;
		$this->query_loop = (array) get_field( Query_Loop::QUERY_LOOP );
		$this->cta        = [];
	}

	public function get_items(): array {
		$query_type = $this->query_loop[ Query_Loop::QUERY_TYPE ] ?? Query_Loop::LATEST;

		if ( empty( $query_type ) || $query_type === Query_Loop::LATEST ) {
			return $this->get_latest_query_items();
		}
		
		if ( $query_type === Query_Loop::AUTOMATIC ) {
			return $this->get_automatic_query_items();
		}

		return $this->get_manual_query_items();
	}
	
	protected function get_latest_query_items(): array {
		$args = [
			'fields'      => 'ids',
			'post_type'   => $this->post_types,
			'post_status' => 'published',
			'order'       => 'DESC',
			'orderby'     => 'date',
			'numberposts' => $this->number_of_posts_display,
		];

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			return [];
		}

		return $this->prepare_posts_for_display( $posts );
	}

	protected function get_automatic_query_items(): array {
		$category_id = $this->query_loop[ Query_Loop::QUERY_LOOP ][ Query_Loop::TAX_ITEMS ] ?? 0;

		if ( (int) $category_id < 1 ) {
			return [];
		}

		if ( is_array( $category_id ) ) {
			$category_id = reset( $category_id );
		}
		
		$args = [
			'fields'      => 'ids',
			'post_type'   => $this->post_types,
			'post_status' => 'published',
			'order'       => 'DESC',
			'orderby'     => 'date',
			'numberposts' => $this->number_of_posts_display,
			'tax_query'   => [
				[
					'taxonomy' => $this->query_loop[ Query_Loop::QUERY_LOOP ][ Query_Loop::TAXONOMIES ],
					'terms'    => $category_id,
				],
			],
		];
		
		if ( $this->exclude_current_post_from_query ) {
			$args['exclude'] = [ get_the_ID(), ];
		}

		$posts = get_posts( $args );

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

}
