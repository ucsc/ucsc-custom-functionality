<?php
/**
 * Plugin Name: UCSC Custom Functionality
 * Plugin URI: https://github.com/ucsc/ucsc-custom-functionality.git
 * Description: Contains custom functionality for UCSC WordPress Websites.
 * Version: 0.1.0
 * Author: UC Santa Cruz
 * Author URI: https://github.com/ucsc
 * License: GPL2
 *
 * @package ucsc
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

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
add_filter( 'http_request_args', 'ucsc_custom_functionality_hidden', 5, 2 );

// Plugin Directory.
define( 'UCSC_DIR', dirname( __FILE__ ) );

// Include Customization files.
// Roles.
if ( file_exists( UCSC_DIR . '/lib/functions/roles.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/roles.php';
}
