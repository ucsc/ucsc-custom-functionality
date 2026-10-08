<?php
/**
 * Tests for lib/functions/settings.php.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Tests\Unit;

use Brain\Monkey\Functions;

/**
 * Covers the plugin file path read by the settings page (#106).
 */
class Settings_Test extends Test_Case {

	/**
	 * Load plugin.php, which includes settings.php, stubbing what it calls
	 * at file scope.
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
	 * The header is read from this plugin's own file, wherever its directory
	 * is, not from a hardcoded directory name under WP_PLUGIN_DIR.
	 *
	 * @return void
	 */
	public function test_settings_page_reads_the_plugin_file_it_was_loaded_from(): void {
		Functions\stubEscapeFunctions();
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'wp_kses_post' )->returnArg();
		Functions\when( 'do_shortcode' )->justReturn( '' );
		Functions\expect( 'get_plugin_data' )
			->once()
			->with( UCSC_DIR . '/plugin.php' )
			->andReturn(
				[
					'Name'        => 'UCSC Custom Functionality',
					'Version'     => '0.0.0',
					'Description' => '',
				]
			);

		ob_start();
		ucsc_render_plugin_settings_page();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'UCSC Custom Functionality', $output );
	}
}
