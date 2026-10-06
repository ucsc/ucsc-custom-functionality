<?php
/**
 * Query_Loop_Controller tests.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit\Components;

use Brain\Monkey\Functions;
use UCSC\Blocks\Blocks\Query_Loop;
use UCSC\Blocks\Components\Query_Loop_Controller;
use UCSC\Blocks\Tests\Unit\Test_Case;

/**
 * Covers the manual-mode guard in Query_Loop_Controller (#104).
 */
class Query_Loop_Controller_Test extends Test_Case {

	/**
	 * Build a controller whose saved query group is the given value.
	 *
	 * The subclass hands resolved IDs straight back, so get_items() exposes
	 * exactly what the base class resolved.
	 *
	 * @param mixed $query_loop What get_field() returns for the query group.
	 *
	 * @return Query_Loop_Controller
	 */
	private function make_controller( $query_loop ): Query_Loop_Controller {
		Functions\when( 'get_field' )->justReturn( $query_loop );

		return new class( [] ) extends Query_Loop_Controller {
			/**
			 * Return the IDs unchanged.
			 *
			 * @param array $posts         Post IDs.
			 * @param bool  $is_auto_query Unused.
			 *
			 * @return array
			 */
			protected function prepare_posts_for_display( array $posts = [], bool $is_auto_query = false ): array {
				return $posts;
			}
		};
	}

	/**
	 * Manual mode with the repeater absent from the saved group yields no
	 * posts, without a warning.
	 *
	 * @return void
	 */
	public function test_manual_mode_with_no_repeater_yields_nothing(): void {
		$controller = $this->make_controller( [ Query_Loop::QUERY_TYPE => Query_Loop::MANUAL ] );

		$this->assertSame( [], $controller->get_items() );
	}

	/**
	 * ACF returns false for an empty repeater; that yields no posts too.
	 *
	 * @return void
	 */
	public function test_manual_mode_with_an_empty_repeater_yields_nothing(): void {
		$controller = $this->make_controller(
			[
				Query_Loop::QUERY_TYPE   => Query_Loop::MANUAL,
				Query_Loop::MANUAL_CARDS => false,
			]
		);

		$this->assertSame( [], $controller->get_items() );
	}

	/**
	 * Saved rows resolve to their post IDs in the arranged order.
	 *
	 * @return void
	 */
	public function test_manual_mode_resolves_rows_to_post_ids_in_order(): void {
		$controller = $this->make_controller(
			[
				Query_Loop::QUERY_TYPE   => Query_Loop::MANUAL,
				Query_Loop::MANUAL_CARDS => [
					[ Query_Loop::MANUAL_CARD => 12 ],
					[ Query_Loop::MANUAL_CARD => 7 ],
				],
			]
		);

		$this->assertSame( [ 12, 7 ], $controller->get_items() );
	}
}
