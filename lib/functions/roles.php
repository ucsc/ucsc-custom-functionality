<?php
/**
 * Roles
 *
 * This file contains any function to add new user roles
 *
 * @package      ucsc
 * @since        0.1.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


/**
 * Adds are new role based on the administrator role capabilities
 *
 * @return void
 */
function ucsc_custom_functionality_site_manager_role() {

	$admin_role = get_role( 'administrator' );

	// Array of capabilities you do not want the new role to have.
	$remove_caps = array(
		'switch_themes'      => 0,
		'edit_themes'        => 0,
		'edit_theme_options' => 0,
		'edit_plugins'       => 0,
	);

	// Run a diff on the admin role capabilities and the removed rules.
	$new_role_caps = array_diff_key( $admin_role->capabilities, $remove_caps );

	// Add the role.
	$new_role = add_role( 'ucsc_site_manager', 'UCSC Site Manager', $new_role_caps );

}

add_action( 'after_setup_theme', 'ucsc_custom_functionality_site_manager_role' );
