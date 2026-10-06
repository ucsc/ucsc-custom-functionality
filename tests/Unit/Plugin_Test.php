<?php
/**
 * Tests for plugin.php.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit;

use Brain\Monkey\Functions;
use WP_Screen;

/**
 * Covers the current-screen guard in ucsc_enqueue_admin_styles() (#104).
 */
class Plugin_Test extends Test_Case {

	/**
	 * Load plugin.php, stubbing what it calls at file scope.
	 *
	 * The file is only ever loaded once per process; the stubs are re-declared
	 * per test because Brain Monkey resets them between tests.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		Functions\when( 'plugin_dir_url' )->justReturn( 'https://example.test/wp-content/plugins/ucsc-custom-functionality/' );
		Functions\when( 'get_file_data' )->justReturn( [ 'Version' => '0.0.0' ] );
		Functions\when( 'add_shortcode' )->justReturn( true );

		require_once dirname( __DIR__, 2 ) . '/plugin.php';
	}

	/**
	 * With no current screen the function bails instead of reading ->base
	 * on null.
	 *
	 * @return void
	 */
	public function test_admin_styles_bail_when_there_is_no_current_screen(): void {
		Functions\when( 'get_current_screen' )->justReturn( null );
		Functions\expect( 'wp_register_style' )->never();
		Functions\expect( 'wp_enqueue_style' )->never();

		ucsc_enqueue_admin_styles( 'index.php' );
	}

	/**
	 * Other admin screens are left alone.
	 *
	 * @return void
	 */
	public function test_admin_styles_are_not_enqueued_on_other_screens(): void {
		$screen       = new WP_Screen();
		$screen->base = 'dashboard';

		Functions\when( 'get_current_screen' )->justReturn( $screen );
		Functions\expect( 'wp_register_style' )->never();
		Functions\expect( 'wp_enqueue_style' )->never();

		ucsc_enqueue_admin_styles( 'index.php' );
	}

	/**
	 * The settings screen gets its stylesheet.
	 *
	 * @return void
	 */
	public function test_admin_styles_are_enqueued_on_the_settings_screen(): void {
		$screen       = new WP_Screen();
		$screen->base = 'settings_page_ucsc-custom-functionality-settings';

		Functions\when( 'get_current_screen' )->justReturn( $screen );
		Functions\expect( 'wp_register_style' )->once()->with( 'ucsc-cf-admin-settings', \Mockery::type( 'string' ), [], '0.0.0' );
		Functions\expect( 'wp_enqueue_style' )->once()->with( 'ucsc-cf-admin-settings' );

		ucsc_enqueue_admin_styles( 'settings_page_ucsc-custom-functionality-settings' );
	}
}
