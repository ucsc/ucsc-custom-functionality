<?php
/**
 * Post_Single template tests.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit\Template;

use Brain\Monkey\Functions;
use UCSC\Blocks\Template\Post_Single;
use UCSC\Blocks\Tests\Unit\Test_Case;
use WP_Block_Template;

/**
 * Covers the slug__in guard in Post_Single::register() (#104).
 */
class Post_Single_Test extends Test_Case {

	/**
	 * The stored template, as register_template() would return it.
	 *
	 * @var WP_Block_Template[]
	 */
	private array $template;

	/**
	 * A Post_Single whose stored template is stubbed, so no database is needed.
	 *
	 * @var Post_Single
	 */
	private Post_Single $post_single;

	/**
	 * Build the template with a stubbed lookup.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->template    = [ new WP_Block_Template() ];
		$template          = $this->template;
		$this->post_single = new class( $template ) extends Post_Single {
			/**
			 * Stubbed stored template.
			 *
			 * @var array
			 */
			private array $stub;

			/**
			 * Store the stub.
			 *
			 * @param array $stub The template array to return.
			 */
			public function __construct( array $stub ) {
				$this->stub = $stub;
			}

			/**
			 * Skip the database lookup.
			 *
			 * @return array
			 */
			public function register_template(): array {
				return $this->stub;
			}
		};
	}

	/**
	 * Callers that omit slug__in get the template rather than a TypeError.
	 *
	 * @return void
	 */
	public function test_register_tolerates_a_query_without_slug_in(): void {
		Functions\when( 'is_single' )->justReturn( true );

		$this->assertSame( $this->template, $this->post_single->register( [], [ 'post_type' => 'post' ], 'wp_template' ) );
	}

	/**
	 * A null query is tolerated too.
	 *
	 * @return void
	 */
	public function test_register_tolerates_a_null_query(): void {
		Functions\when( 'is_single' )->justReturn( true );

		$this->assertSame( $this->template, $this->post_single->register( [], null, 'wp_template' ) );
	}

	/**
	 * A malformed slug__in is treated as absent.
	 *
	 * @return void
	 */
	public function test_register_tolerates_a_non_array_slug_in(): void {
		Functions\when( 'is_single' )->justReturn( true );

		$this->assertSame( $this->template, $this->post_single->register( [], [ 'slug__in' => 'embed-post' ], 'wp_template' ) );
	}

	/**
	 * Post embeds keep their own template.
	 *
	 * @return void
	 */
	public function test_register_steps_aside_for_the_embed_template(): void {
		Functions\when( 'is_single' )->justReturn( true );

		$this->assertSame( [ 'existing' ], $this->post_single->register( [ 'existing' ], [ 'slug__in' => [ 'embed-post' ] ], 'wp_template' ) );
	}

	/**
	 * Outside single posts the query result passes through.
	 *
	 * @return void
	 */
	public function test_register_passes_through_when_not_single(): void {
		Functions\when( 'is_single' )->justReturn( false );

		$this->assertSame( [ 'existing' ], $this->post_single->register( [ 'existing' ], [ 'slug__in' => [ 'single' ] ], 'wp_template' ) );
	}
}
