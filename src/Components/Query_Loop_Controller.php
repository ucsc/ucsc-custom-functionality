<?php
/**
 * Query-loop controller base class.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Query_Loop;
use UCSC\Blocks\Components\Traits\With_CTA;

/**
 * Resolves a query-loop block's editor selection into post IDs.
 *
 * Mirrors Query_Loop on the field side: whichever of the three modes the
 * editor picked, this class produces a list of post IDs and hands it to the
 * subclass, which shapes each post into whatever its view needs.
 *
 * Subclasses override the protected properties to change how many posts are
 * shown and which post types are queried.
 */
abstract class Query_Loop_Controller {

	use With_CTA;

	/**
	 * The block instance passed to the render callback.
	 *
	 * @var array
	 */
	protected array $block;
	/**
	 * The call-to-action link, when the block has one.
	 *
	 * @var array
	 */
	protected array $cta;
	/**
	 * The saved query configuration group.
	 *
	 * @var array
	 */
	protected array $query_loop;
	/**
	 * Whether automatic mode omits the post being viewed.
	 *
	 * @var bool
	 */
	protected bool $exclude_current_post_from_query = true;

	/**
	 * How many posts to display. Override per block.
	 *
	 * @var int
	 */
	protected int $number_of_posts_display = 4;
	/**
	 * Post types to query. Override per block.
	 *
	 * @var string[]
	 */
	protected array $post_types = [ 'post' ];

	/**
	 * Shape resolved post IDs into the structure the view renders.
	 *
	 * Implemented per block.
	 *
	 * @param array $posts         Post IDs to prepare.
	 * @param bool  $is_auto_query Whether the IDs came from the automatic query.
	 *
	 * @return array
	 */
	abstract protected function prepare_posts_for_display( array $posts = [], bool $is_auto_query = false ): array;

	/**
	 * Read the saved query configuration.
	 *
	 * @param mixed $block The block instance supplied by the render callback.
	 */
	public function __construct( $block ) {
		$this->block      = (array) $block;
		$this->query_loop = (array) get_field( Query_Loop::QUERY_LOOP );
		$this->cta        = [];
	}

	/**
	 * Resolve the editor's selection into display-ready items.
	 *
	 * Falls back to the latest-posts mode when nothing has been chosen.
	 *
	 * @return array
	 */
	public function get_items(): array {
		$query_type = $this->query_loop[ Query_Loop::QUERY_TYPE ] ?? Query_Loop::LATEST;

		if ( empty( $query_type ) || Query_Loop::LATEST === $query_type ) {
			return $this->get_latest_query_items();
		}

		if ( Query_Loop::AUTOMATIC === $query_type ) {
			return $this->get_automatic_query_items();
		}

		return $this->get_manual_query_items();
	}

	/**
	 * The most recent published posts.
	 *
	 * @return array
	 */
	protected function get_latest_query_items(): array {
		$args = [
			'fields'      => 'ids',
			'post_type'   => $this->post_types,
			'post_status' => 'publish',
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

	/**
	 * Posts belonging to the chosen taxonomy term.
	 *
	 * Optionally excludes the post being viewed, so a related-stories block on
	 * a post does not list that post.
	 *
	 * @return array
	 */
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
			'post_status' => 'publish',
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
			$args['exclude'] = [ get_the_ID() ];
		}

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			return [];
		}

		return $this->prepare_posts_for_display( $posts );
	}

	/**
	 * The editor's hand-picked posts, in the order they were arranged.
	 *
	 * Note: the repeater index is read unguarded, so manual mode with no rows
	 * saved raises a warning. Tracked in #104.
	 *
	 * @return array
	 */
	protected function get_manual_query_items(): array {
		$posts = $this->query_loop[ Query_Loop::MANUAL_CARDS ];

		if ( empty( $posts ) ) {
			return [];
		}

		$posts = array_column( $posts, 'manual_card' );

		return $this->prepare_posts_for_display( $posts );
	}
}
