<?php
/**
 * Base test case.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * Sets up Brain Monkey and the WordPress sanitisers the code under test uses.
 *
 * The sanitisers are given working implementations rather than canned return
 * values, so tests exercise what the guards actually do to their input.
 */
abstract class Test_Case extends TestCase {

	use MockeryPHPUnitIntegration;

	/**
	 * Boot Brain Monkey and register sanitiser implementations.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'wp_unslash' )->alias(
			static function ( $value ) {
				return is_array( $value ) ? array_map( 'stripslashes', $value ) : stripslashes( $value );
			}
		);

		Functions\when( 'sanitize_key' )->alias(
			static function ( $key ): string {
				return is_scalar( $key ) ? preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) ) : '';
			}
		);

		Functions\when( 'sanitize_text_field' )->alias(
			static function ( $text ): string {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- wp_strip_all_tags() is what is being stood in for.
				return trim( preg_replace( '/\s+/', ' ', strip_tags( (string) $text ) ) );
			}
		);
	}

	/**
	 * Tear down Brain Monkey and clear the request body.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		$_POST = [];
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Set a private or protected property on an object.
	 *
	 * @param object $instance The object.
	 * @param string $name     Property name.
	 * @param mixed  $value    Value to set.
	 *
	 * @return void
	 */
	protected function set_property( object $instance, string $name, $value ): void {
		$property = new \ReflectionProperty( $instance, $name );
		$property->setValue( $instance, $value );
	}
}
