<?php
/**
 * News_Block_Controller tests.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit\Components;

use Brain\Monkey\Functions;
use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Components\News_Block_Controller;
use UCSC\Blocks\Tests\Unit\Test_Case;

/**
 * Covers the number-of-posts fallback in News_Block_Controller (#106).
 */
class News_Block_Controller_Test extends Test_Case {

	/**
	 * Build a configured controller whose saved number of posts is the given
	 * value, with a full page of items already cached.
	 *
	 * @param mixed $posts_per_page What get_field() returns for the number of posts.
	 *
	 * @return News_Block_Controller
	 */
	private function make_controller( $posts_per_page ): News_Block_Controller {
		$fields = [
			News_Block::TAXONOMIES     => 'categories',
			News_Block::TAX_ITEMS      => [ 1 ],
			News_Block::POSTS_PER_PAGE => $posts_per_page,
		];

		Functions\when( 'get_field' )->alias(
			static fn( string $name ) => $fields[ $name ] ?? null
		);
		Functions\when( 'get_transient' )->justReturn(
			array_fill( 0, News_Block_Controller::PER_PAGE, [ 'title' => 'Post' ] )
		);

		return new News_Block_Controller( [] );
	}

	/**
	 * Values that are not a positive number fall back to PER_PAGE instead
	 * of rendering an empty block.
	 *
	 * @return array<string, array{mixed}>
	 */
	public static function unusable_values(): array {
		return [
			'never saved'  => [ null ],
			'false'        => [ false ],
			'empty string' => [ '' ],
			'zero'         => [ '0' ],
			'negative'     => [ -3 ],
			'non-numeric'  => [ 'six' ],
			'array'        => [ [ 6 ] ],
		];
	}

	/**
	 * Blocks with no usable saved value render a full page of posts.
	 *
	 * @dataProvider unusable_values
	 *
	 * @param mixed $value The saved number of posts.
	 *
	 * @return void
	 */
	#[\PHPUnit\Framework\Attributes\DataProvider( 'unusable_values' )]
	public function test_unusable_value_falls_back_to_per_page( $value ): void {
		$this->assertCount( News_Block_Controller::PER_PAGE, $this->make_controller( $value )->get_items() );
	}

	/**
	 * A saved choice is honoured, whether ACF returns it as a string or an int.
	 *
	 * @return void
	 */
	public function test_saved_value_is_honoured(): void {
		$this->assertCount( 6, $this->make_controller( '6' )->get_items() );
		$this->assertCount( 3, $this->make_controller( 3 )->get_items() );
	}

	/**
	 * Counts above PER_PAGE are capped, since no more are fetched.
	 *
	 * @return void
	 */
	public function test_value_above_per_page_is_capped(): void {
		$this->assertCount( News_Block_Controller::PER_PAGE, $this->make_controller( '12' )->get_items() );
	}
}
