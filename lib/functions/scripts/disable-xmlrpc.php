<?php

/**
 * Disable XMLRPC
 *
 * /xmlrpc.php can be used to brute force admin usernames and passwords.
 *
 * see: https://pantheon.io/docs/wordpress-best-practices#avoid-xml-rpc-attacks
 *
 * XMLRPC is disabled by default. Site administrators can re-enable it from
 * Settings → UCSC Custom Functionality.
 */

if ( ! (bool) get_option( 'ucsc_enable_xmlrpc', false ) ) {
	add_filter(
		'xmlrpc_methods',
		function () {
			return array();
		},
		PHP_INT_MAX
	);

	// Removes the link from the <head>.
	// Avoids a11y issue with broken link
	remove_action( 'wp_head', 'rsd_link' );
}