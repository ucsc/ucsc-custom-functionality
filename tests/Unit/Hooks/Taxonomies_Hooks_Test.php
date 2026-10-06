<?php
/**
 * Taxonomies_Hooks tests.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit\Hooks;

use Brain\Monkey\Functions;
use UCSC\Blocks\Hooks\Taxonomies_Hooks;
use UCSC\Blocks\Tests\Unit\Test_Case;

/**
 * Covers the guarded AJAX term search against local taxonomies (#103).
 */
class Taxonomies_Hooks_Test extends Test_Case {

	/**
	 * Every test here runs the AJAX branch.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}
	}

	/**
	 * With no taxonomy posted, no query runs.
	 *
	 * @return void
	 */
	public function test_search_is_a_no_op_when_no_taxonomy_is_posted(): void {
		Functions\expect( 'get_terms' )->never();

		$this->assertSame( [], ( new Taxonomies_Hooks() )->load_search_tax_items( [] ) );
	}

	/**
	 * Taxonomies hidden from the dropdown cannot be searched either.
	 *
	 * @return void
	 */
	public function test_search_rejects_a_restricted_taxonomy(): void {
		$_POST['taxonomy_selected'] = 'nav_menu';

		Functions\expect( 'taxonomy_exists' )->never();
		Functions\expect( 'get_terms' )->never();

		$this->assertSame( [], ( new Taxonomies_Hooks() )->load_search_tax_items( [] ) );
	}

	/**
	 * A taxonomy that is not registered is rejected.
	 *
	 * @return void
	 */
	public function test_search_rejects_an_unregistered_taxonomy(): void {
		$_POST['taxonomy_selected'] = 'category/../secret';

		Functions\expect( 'taxonomy_exists' )->once()->with( 'categorysecret' )->andReturn( false );
		Functions\expect( 'get_terms' )->never();

		$this->assertSame( [], ( new Taxonomies_Hooks() )->load_search_tax_items( [] ) );
	}

	/**
	 * A registered, unrestricted taxonomy is queried with the sanitised
	 * search string.
	 *
	 * @return void
	 */
	public function test_search_queries_a_registered_taxonomy(): void {
		$_POST['taxonomy_selected'] = 'Category';
		$_POST['s']                 = '  Arts <b>and</b>  Sciences ';

		Functions\expect( 'taxonomy_exists' )->once()->with( 'category' )->andReturn( true );
		Functions\expect( 'get_terms' )
			->once()
			->with(
				[
					'taxonomy'   => 'category',
					'hide_empty' => false,
					'search'     => 'Arts and Sciences',
				]
			)
			->andReturn(
				[
					(object) [
						'term_id' => 3,
						'name'    => 'Arts and Sciences',
					],
				]
			);

		$result = ( new Taxonomies_Hooks() )->load_search_tax_items( [] );

		$this->assertSame(
			[
				[
					'id'   => 3,
					'text' => 'Arts and Sciences',
				],
			],
			$result['results']
		);
	}

	/**
	 * A search that matches nothing returns an empty result set, not the
	 * untouched shortcut.
	 *
	 * @return void
	 */
	public function test_search_returns_empty_results_when_nothing_matches(): void {
		$_POST['taxonomy_selected'] = 'category';

		Functions\when( 'taxonomy_exists' )->justReturn( true );
		Functions\when( 'get_terms' )->justReturn( [] );

		$this->assertSame( [ 'results' => [] ], ( new Taxonomies_Hooks() )->load_search_tax_items( [] ) );
	}
}
