<?php
/**
 * Plugin Name: UCSC Custom Functionality
 * Plugin URI: https://github.com/ucsc/ucsc-custom-functionality.git
 * Description: Adds custom functionality to UCSC WordPress Websites.
 * Version: 1.6.0
 * Author: UC Santa Cruz
 * Author URI: https://github.com/ucsc
 * Update URI: https://github.com/ucsc
 * License: GPL2
 *
 * @package ucsc-custom-functionality
 */

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

if ( ! function_exists( 'ucsc_enqueue_admin_styles' ) ) {
	/**
	* Enqueue admin settings styles
	*
	* No styles are enqueued for raw HTML in setting panel.
	* In order to output HTML in the settings panel we need some basic styles.
	*
	* @since 1.7.0
	* @author UCSC
	* @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/#Example:_Load_CSS_File_from_a_plugin_on_specific_Admin_Page
	*
	*/

	function ucsc_enqueue_admin_styles($hook){
		$settings_css = plugin_dir_url(__FILE__) . 'lib/css/admin-settings.css';
		$current_screen = get_current_screen();
		// Check if it's "?page=ucsc-custom-functionality-settings." If not, just empty return. 
		if ( strpos($current_screen->base, 'ucsc-custom-functionality-settings') === false) {
			return;
		} else {

			// Load css.
			wp_register_style( 'ucsc-cf-admin-settings', $settings_css,);
			wp_enqueue_style( 'ucsc-cf-admin-settings');
		}
	}
}
add_action( 'admin_enqueue_scripts', 'ucsc_enqueue_admin_styles' );

/** 
 * Add link to Settings page from Plugins
 */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ucsc_custom_functionality_plugin_action_links' );

function ucsc_custom_functionality_plugin_action_links( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'ucsc-custom-functionality-settings',
		get_admin_url() . 'options-general.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}
