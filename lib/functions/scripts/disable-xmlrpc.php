<?php
/**
 * Disable XMLRPC
 *
 * /xmlrpc.php can be used to brute force admin usernames and passwords.
 *
 * see: https://pantheon.io/docs/wordpress-best-practices#avoid-xml-rpc-attacks
 *
 * XMLRPC is always disabled when this plugin is active.
 *
 * @package      ucsc
 * @since        0.1.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

add_filter(
	'xmlrpc_methods',
	function () {
		return array();
	},
	PHP_INT_MAX
);

// Removes the link from the <head>.
// Avoids an accessibility issue with the broken link.
remove_action( 'wp_head', 'rsd_link' );
