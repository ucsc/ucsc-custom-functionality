<?php
/**
 * Block_Templates tests.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit\Template;

use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use UCSC\Blocks\Template\Block_Templates;
use UCSC\Blocks\Tests\Unit\Test_Case;

/**
 * Covers registering the plugin templates with core (#109).
 */
class Block_Templates_Test extends Test_Case {

	/**
	 * Define the plugin directory when plugin.php has not been loaded.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'UCSC_DIR' ) ) {
			define( 'UCSC_DIR', dirname( __DIR__, 3 ) );
		}

		Functions\when( '__' )->returnArg();
	}

	/**
	 * Every template has a bundled HTML file named after its slug.
	 *
	 * @return void
	 */
	public function test_every_template_has_a_file(): void {
		foreach ( array_keys( ( new Block_Templates() )->get_templates() ) as $slug ) {
			$this->assertFileExists( UCSC_DIR . '/src/views/templates/' . $slug . '.html' );
		}
	}

	/**
	 * Each template is registered under the plugin namespace with its
	 * bundled HTML as content.
	 *
	 * @return void
	 */
	public function test_templates_are_registered_under_the_plugin_namespace(): void {
		$registered = [];

		Functions\when( 'register_block_template' )->alias(
			static function ( string $name, array $args ) use ( &$registered ): void {
				$registered[ $name ] = $args;
			}
		);

		( new Block_Templates() )->register();

		$this->assertSame(
			[
				'ucsc-custom-functionality//single-post',
				'ucsc-custom-functionality//archive-photo_of_the_week',
			],
			array_keys( $registered )
		);

		foreach ( $registered as $name => $args ) {
			$slug = explode( '//', $name )[1];

			$this->assertStringEqualsFile( UCSC_DIR . '/src/views/templates/' . $slug . '.html', $args['content'] );
			$this->assertNotEmpty( $args['title'] );
		}
	}

	/**
	 * The bundled templates do not pin their template parts to a theme.
	 *
	 * @return void
	 */
	public function test_template_parts_are_not_pinned_to_a_theme(): void {
		foreach ( array_keys( ( new Block_Templates() )->get_templates() ) as $slug ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a fixture inside the plugin.
			$this->assertStringNotContainsString( '"theme":', (string) file_get_contents( UCSC_DIR . '/src/views/templates/' . $slug . '.html' ) );
		}
	}

	/**
	 * Before WordPress 6.7 there is no register_block_template(), and
	 * registering does nothing rather than failing.
	 *
	 * Runs in its own process because a function stubbed by another test
	 * cannot be undefined.
	 *
	 * @return void
	 */
	#[RunInSeparateProcess]
	#[PreserveGlobalState( false )]
	public function test_register_does_nothing_before_wordpress_6_7(): void {
		$this->assertFalse( function_exists( 'register_block_template' ) );

		( new Block_Templates() )->register();

		$this->addToAssertionCount( 1 );
	}
}
