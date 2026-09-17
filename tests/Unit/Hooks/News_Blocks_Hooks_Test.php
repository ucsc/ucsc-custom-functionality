<?php
/**
 * News_Blocks_Hooks tests.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit\Hooks;

use Brain\Monkey\Functions;
use Mockery;
use UCSC\Blocks\Hooks\News_Blocks_Hooks;
use UCSC\Blocks\Request\News_Request;
use UCSC\Blocks\Tests\Unit\Test_Case;

/**
 * Covers the guarded AJAX term search against the remote news site (#103),
 * and the request gating and term labelling the dropdown fix introduced.
 */
class News_Blocks_Hooks_Test extends Test_Case {

	/**
	 * Transient key for the taxonomy choice list.
	 *
	 * @var string
	 */
	private const TAXONOMIES_KEY = 'news_query_block_taxonomies';

	/**
	 * Transient key prefix for a taxonomy's term list.
	 *
	 * @var string
	 */
	private const TERMS_KEY = 'news_query_block_taxonomy_items_';

	/**
	 * Taxonomy choices as the dropdown offers them: label keyed by REST base.
	 *
	 * @var string[]
	 */
	private const CHOICES = [
		'categories' => 'Categories',
		'tags'       => 'Tags',
	];

	/**
	 * Present every request as ACF's select-search AJAX call.
	 *
	 * That is what is_term_search_request() looks for: an AJAX request whose
	 * action is ACF's, since block previews arrive over admin-ajax too.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		Functions\when( 'wp_doing_ajax' )->justReturn( true );

		$_POST['action'] = 'acf/fields/select/query';
	}

	/**
	 * Build the hooks object with a mocked remote client.
	 *
	 * @param Mockery\MockInterface|null $request The client to inject, if any.
	 *
	 * @return News_Blocks_Hooks
	 */
	private function make_hooks( ?Mockery\MockInterface $request = null ): News_Blocks_Hooks {
		$hooks = new News_Blocks_Hooks();

		$this->set_property( $hooks, 'request', $request ?? Mockery::mock( News_Request::class )->shouldNotReceive( 'request' )->getMock() );

		return $hooks;
	}

	/**
	 * Serve transients from a fixed map.
	 *
	 * @param array $transients Values keyed by transient key.
	 *
	 * @return void
	 */
	private function stub_transients( array $transients ): void {
		Functions\when( 'get_transient' )->alias(
			static function ( string $key ) use ( $transients ) {
				return $transients[ $key ] ?? false;
			}
		);
	}

	/**
	 * Requests that are not ACF's term search are left to ACF.
	 *
	 * Block previews are fetched over admin-ajax as well, so the shortcut has
	 * to pass through untouched rather than become an empty result set.
	 *
	 * @return void
	 */
	public function test_search_ignores_requests_that_are_not_the_acf_term_search(): void {
		unset( $_POST['action'] );
		$_POST['taxonomy_selected'] = 'categories';

		Functions\expect( 'get_transient' )->never();

		$shortcut = [ 'results' => [ 'untouched' ] ];

		$this->assertSame( $shortcut, $this->make_hooks()->load_search_tax_items( $shortcut ) );
	}

	/**
	 * With no taxonomy posted, nothing is looked up.
	 *
	 * @return void
	 */
	public function test_search_is_a_no_op_when_no_taxonomy_is_posted(): void {
		Functions\expect( 'get_transient' )->never();

		$this->assertSame( [ 'results' => [] ], $this->make_hooks()->load_search_tax_items( [] ) );
	}

	/**
	 * A taxonomy the dropdown never offered is rejected before any request.
	 *
	 * @return void
	 */
	public function test_search_rejects_a_taxonomy_that_was_not_offered(): void {
		$_POST['taxonomy_selected'] = 'users';

		$this->stub_transients( [ self::TAXONOMIES_KEY => self::CHOICES ] );
		Functions\expect( 'set_transient' )->never();

		$this->assertSame( [ 'results' => [] ], $this->make_hooks()->load_search_tax_items( [] ) );
	}

	/**
	 * Path segments cannot be smuggled into the outbound request.
	 *
	 * @return void
	 */
	public function test_search_rejects_a_traversal_attempt(): void {
		$_POST['taxonomy_selected'] = '../../../wp-json/wp/v2/users';

		$this->stub_transients( [ self::TAXONOMIES_KEY => self::CHOICES ] );
		Functions\expect( 'set_transient' )->never();

		$this->assertSame( [ 'results' => [] ], $this->make_hooks()->load_search_tax_items( [] ) );
	}

	/**
	 * An offered taxonomy is accepted, and the cached term list is filtered
	 * by the posted search string.
	 *
	 * @return void
	 */
	public function test_search_filters_the_cached_terms_of_an_offered_taxonomy(): void {
		$_POST['taxonomy_selected'] = 'categories';

		$this->stub_transients(
			[
				self::TAXONOMIES_KEY           => self::CHOICES,
				self::TERMS_KEY . 'categories' => [
					1 => 'Arts and Sciences',
					2 => 'Engineering',
				],
			]
		);

		$result = $this->make_hooks()->load_search_tax_items( [], [ 's' => 'arts and' ] );

		$this->assertSame(
			[
				[
					'id'   => 1,
					'text' => 'Arts and Sciences',
				],
			],
			$result['results']
		);
	}

	/**
	 * The posted taxonomy is unslashed and normalised before the allow-list
	 * check, and the posted search string is unslashed.
	 *
	 * @return void
	 */
	public function test_search_unslashes_and_normalises_posted_values(): void {
		$_POST['taxonomy_selected'] = 'CATEGORIES\\\\';

		$this->stub_transients(
			[
				self::TAXONOMIES_KEY           => self::CHOICES,
				self::TERMS_KEY . 'categories' => [
					1 => "O'Brien",
					2 => 'Engineering',
				],
			]
		);

		$result = $this->make_hooks()->load_search_tax_items( [], [ 's' => 'o\\\'b' ] );

		$this->assertSame( [ 1 ], array_column( $result['results'], 'id' ) );
	}

	/**
	 * When the term list is not cached it is fetched once, cached unfiltered,
	 * and only then filtered — so later searches do not inherit this filter.
	 *
	 * @return void
	 */
	public function test_search_caches_the_unfiltered_term_list(): void {
		$_POST['taxonomy_selected'] = 'categories';

		$this->stub_transients( [ self::TAXONOMIES_KEY => self::CHOICES ] );

		$request = Mockery::mock( News_Request::class );
		$request->shouldReceive( 'request' )
			->once()
			->with( 'wp-json/wp/v2/categories', [ 'per_page' => 100 ], true )
			->andReturn(
				[
					[
						'id'   => 1,
						'name' => 'Arts',
					],
					[
						'id'   => 2,
						'name' => 'Biology',
					],
				]
			);

		Functions\expect( 'set_transient' )
			->once()
			->with(
				self::TERMS_KEY . 'categories',
				[
					1 => 'Arts',
					2 => 'Biology',
				],
				20 * MINUTE_IN_SECONDS
			);

		$result = $this->make_hooks( $request )->load_search_tax_items( [], [ 's' => 'bio' ] );

		$this->assertSame( [ 2 ], array_column( $result['results'], 'id' ) );
	}

	/**
	 * The taxonomy dropdown only offers taxonomies in News_Block::ALLOWED_TAX,
	 * keyed by REST base.
	 *
	 * @return void
	 */
	public function test_taxonomy_choices_are_limited_to_the_allow_list(): void {
		$this->stub_transients( [] );

		$request = Mockery::mock( News_Request::class );
		$request->shouldReceive( 'request' )
			->once()
			->with( News_Request::TAXONOMY_ENDPOINT, [ 'type' => 'post' ] )
			->andReturn(
				[
					'category' => [
						'name'      => 'Categories',
						'rest_base' => 'categories',
					],
					'nav_menu' => [
						'name'      => 'Navigation Menus',
						'rest_base' => 'menus',
					],
					'post_tag' => [
						'name'      => 'Tags',
						'rest_base' => 'tags',
					],
				]
			);

		Functions\expect( 'set_transient' )
			->once()
			->with( self::TAXONOMIES_KEY, self::CHOICES, 20 * MINUTE_IN_SECONDS );

		$field = $this->make_hooks( $request )->load_taxonomies( [ 'choices' => [] ] );

		$this->assertSame( self::CHOICES, $field['choices'] );
	}

	/**
	 * A terms field with nothing selected needs no choices at all.
	 *
	 * @return void
	 */
	public function test_terms_field_is_left_alone_when_nothing_is_selected(): void {
		Functions\expect( 'get_transient' )->never();

		$field = $this->make_hooks()->prepare_tax_items(
			[
				'value'   => '',
				'choices' => [],
			]
		);

		$this->assertSame( [], $field['choices'] );
	}

	/**
	 * Selected terms are labelled from the block's own taxonomy.
	 *
	 * @return void
	 */
	public function test_terms_field_is_labelled_from_the_blocks_taxonomy(): void {
		Functions\when( 'get_field' )->justReturn( 'tags' );

		$this->stub_transients( [ self::TERMS_KEY . 'tags' => [ 9 => 'Research' ] ] );

		$field = $this->make_hooks()->prepare_tax_items(
			[
				'value'   => [ 9 ],
				'choices' => [],
			]
		);

		$this->assertSame( [ 9 => 'Research' ], $field['choices'] );
	}

	/**
	 * A block that has not chosen a taxonomy falls back to categories, rather
	 * than looking up the empty string.
	 *
	 * @return void
	 */
	public function test_terms_field_falls_back_to_the_default_taxonomy(): void {
		Functions\when( 'get_field' )->justReturn( null );

		$this->stub_transients( [ self::TERMS_KEY . 'categories' => [ 1 => 'Arts' ] ] );

		$field = $this->make_hooks()->prepare_tax_items(
			[
				'value'   => [ 1 ],
				'choices' => [],
			]
		);

		$this->assertSame( [ 1 => 'Arts' ], $field['choices'] );
	}
}
