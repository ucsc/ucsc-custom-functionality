<?php
/**
 * Plugin Name: UCSC Main Core Functionality Plugin
 * Plugin URI: https://github.com/ucsc/ucsc-main-core-functionality-plugin.git
 * Description: Contains custom functionality for UCSC Websites. Theme independent.
 * Version: 0.0.0--beta
 * Author: UC Santa Cruz
 * Author URI: https://github.com/ucsc
 * License: GPL2
 *
 * @package SC_Custom_Functionality
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

function sc_custom_functionality_hidden( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) ) {
		return $r; // Not a plugin update request. Bail immediately.
	}
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'sc_custom_functionality_hidden', 5, 2 );

// Plugin Directory.
define( 'SC_DIR', dirname( __FILE__ ) );

// Include Customization files.
// Roles.
if ( file_exists( SC_DIR . '/lib/functions/roles.php' ) ) {
			include_once SC_DIR . '/lib/functions/roles.php';
}
