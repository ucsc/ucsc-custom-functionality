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
		'switch_themes' => 0,
		'edit_themes'   => 0,
		'edit_plugins'  => 0,
	);

	// Run a diff on the admin role capabilities and the removed rules.
	$new_role_caps = array_diff_key( $admin_role->capabilities, $remove_caps );

	// Add the role.
	$new_role = add_role( 'ucsc_site_manager', 'UCSC Site Manager', $new_role_caps );

}

add_action( 'init', 'ucsc_custom_functionality_site_manager_role' );


function ucsc_custom_functionality_remove_role() {
	wp_roles()->remove_role( 'ucsc_site_manager' );
}

register_deactivation_hook( UCSC_DIR . '/plugin.php', 'ucsc_custom_functionality_remove_role' );


/**
 * Deny access to 'administrator' for other roles
 * so others with edit_users capability, cannot edit others
 * to be administrators
 * see: https://wordpress.stackexchange.com/questions/43260/prevent-or-disable-creating-new-users-or-changing-roles-of-existing-users-to-adm
 * 
 * @since   0.1
 * @param   (array) $all_roles
 * @return  (array) $all_roles
 */
function ucsc_deny_change_to_admin( $all_roles )
{
    if ( ! current_user_can('administrator') )
        unset( $all_roles['administrator'] );

    return $all_roles;
}
function ucsc_deny_rolechange()
{
    add_filter( 'editable_roles', 'ucsc_deny_change_to_admin' );
}
add_action( 'after_setup_theme', 'ucsc_deny_rolechange' );