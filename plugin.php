<?php
/**
 * Plugin Name: UCSC Custom Functionality
 * Plugin URI: https://github.com/ucsc/ucsc-custom-functionality.git
 * Description: Adds custom functionality to UCSC WordPress Websites.
 * Version: 1.6.0
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
	* @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
	*
	* @param array  $r, request arguments
	* @param string $url, request url
	* @return array request arguments
	*/

	function ucsc_custom_functionality_hidden( $r, $url ) {
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

// Include Customization files.

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

