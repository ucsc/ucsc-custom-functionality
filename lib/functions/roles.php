<?php
/**
 * Roles
 *
 * This file contains any general functions
 *
 * @package      SC_Custom_Functionality
 * @since        1.0.0
 * @link         https://github.com/ucsc/ucsc-main-core-functionality-plugin.git
 * @author       UCSC
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


/**
 * Adds are new role based on the administrator role capabilities
 *
 * @return void
 */
function sc_site_admin_role() {

	$admin_role = get_role( 'administrator' );

	// Array of capabilities you do not want the new role to have.
	$remove_caps = array(
		'switch_themes'    => 0,
		'edit_themes'      => 0,
		'activate_plugins' => 0,
		'edit_plugins'     => 0,
		'manage_options'   => 0,
	);

	// Run a diff on the admin role capabilities and the removed rules.
	$new_role_caps = array_diff_key( $admin_role->capabilities, $remove_caps );

	// Add the role.
	$new_role = add_role( 'Site Administrator', 'Site Administrator', $new_role_caps );

}

add_action( 'after_setup_theme', 'sc_site_admin_role' );
