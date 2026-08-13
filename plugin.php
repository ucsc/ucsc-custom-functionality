<?php
/**
 * Plugin Name: UCSC Custom Functionality
 * Plugin URI:  https://github.com/ucsc/ucsc-custom-functionality.git
 * Description: Adds custom functionality to UCSC WordPress Websites.
 * Version: 2.0.6
 * Author:      UC Santa Cruz
 * Author URI:  https://github.com/ucsc
 * License:     GPL2
 *
 * Entry point. Defines the plugin constants, includes the procedural
 * lib/functions/ features, and boots the namespaced src/ layer through
 * Core once ACF PRO is confirmed present.
 *
 * @package ucsc
 */

declare(strict_types=1);

// Set plugin directory.
define( 'UCSC_DIR', __DIR__ );
define( 'UCSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Read the version from this file's own header, so it always matches the
// value the release tooling bumps and cannot drift out of sync.
define( 'UCSC_VERSION', get_file_data( __FILE__, array( 'Version' => 'Version' ) )['Version'] );

// Include Customization files.

if ( file_exists( UCSC_DIR . '/vendor/autoload.php' ) ) {
	include_once UCSC_DIR . '/vendor/autoload.php';
}

// Shortcodes.
if ( file_exists( UCSC_DIR . '/lib/functions/shortcodes.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/shortcodes.php';
}

// Admin options.
if ( file_exists( UCSC_DIR . '/lib/functions/admin-menus.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/admin-menus.php';
}

// Scripts.
if ( file_exists( UCSC_DIR . '/lib/functions/scripts.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts.php';
}

// Settings.
if ( file_exists( UCSC_DIR . '/lib/functions/settings.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/settings.php';
}

if ( ! function_exists( 'ucsc_enqueue_admin_styles' ) ) {
	/**
	 * Enqueue admin settings styles
	 *
	 * No styles are enqueued for raw HTML in setting panel.
	 * In order to output HTML in the settings panel we need some basic styles.
	 *
	 * @since 1.7.0
	 *
	 * @author UCSC
	 *
	 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/#Example:_Load_CSS_File_from_a_plugin_on_specific_Admin_Page
	 *
	 * @param string $hook The current admin page hook suffix. Unused; the screen
	 *                     is resolved through get_current_screen() instead.
	 */
	function ucsc_enqueue_admin_styles( $hook ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- signature fixed by the admin_enqueue_scripts contract.
		$settings_css   = plugin_dir_url( __FILE__ ) . 'lib/css/admin-settings.css';
		$current_screen = get_current_screen();
		// Check if it's "?page=ucsc-custom-functionality-settings." If not, just empty return.
		if ( strpos( $current_screen->base, 'ucsc-custom-functionality-settings' ) === false ) {
			return;
		}

		// Load css.
		wp_register_style( 'ucsc-cf-admin-settings', $settings_css, array(), UCSC_VERSION );
		wp_enqueue_style( 'ucsc-cf-admin-settings' );
	}
}
add_action( 'admin_enqueue_scripts', 'ucsc_enqueue_admin_styles' );

add_action(
	'plugins_loaded',
	static function (): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		\UCSC\Blocks\Core::instance()->init();
	},
	100,
	0
);
