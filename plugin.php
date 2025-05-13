<?php declare(strict_types=1);

/**
 * Plugin Name: UCSC Custom Functionality
 * Plugin URI: https://github.com/ucsc/ucsc-custom-functionality.git
 * Description: Adds custom functionality to UCSC WordPress Websites.
 * Version: 1.9.1
 * Author: UC Santa Cruz
 * Author URI: https://github.com/ucsc
 * License: GPL2
 *
 * @package ucsc-custom-functionality
 */

if ( ! function_exists( 'ucsc_custom_functionality_hidden' ) ) {
	/**
	* Don't Update Plugin
	*
	* @since 1.0.0
	*
	* This prevents you being prompted to update if there's a public plugin
	* with the same name.
	*
	* @author Mark Jaquith
	 *
	 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
	*
	* @param array  $r, request arguments
	* @param string $url, request url
	 *
	 * @return array request arguments
	*/

	function ucsc_custom_functionality_hidden( array $r, string $url ): array {
		if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) ) {
			return $r; // Not a plugin update request. Bail immediately.
		}
		$plugins = unserialize( $r['body']['plugins'] );
		unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
		unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
		$r['body']['plugins'] = serialize( $plugins );

		return $r;
	}
}

add_filter( 'http_request_args', 'ucsc_custom_functionality_hidden', 5, 2 );

// Set plugin directory.
define( 'UCSC_DIR', dirname( __FILE__ ) );
define( 'UCSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

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
	 */

	function ucsc_enqueue_admin_styles($hook): void {
		$settings_css   = plugin_dir_url( __FILE__ ) . 'lib/css/admin-settings.css';
		$current_screen = get_current_screen();
		// Check if it's "?page=ucsc-custom-functionality-settings." If not, just empty return. 
		if ( strpos( $current_screen->base, 'ucsc-custom-functionality-settings' ) === false ) {
			return;
		}

		// Load css.
		wp_register_style( 'ucsc-cf-admin-settings', $settings_css, );
		wp_enqueue_style( 'ucsc-cf-admin-settings' );
	}
}
add_action( 'admin_enqueue_scripts', 'ucsc_enqueue_admin_styles' );

add_action( 'plugins_loaded', static function (): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	\UCSC\Blocks\Core::instance()->init();
}, 100, 0 );
