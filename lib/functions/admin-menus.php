<?php
/**
 * Customize admin settings
 *
 * This file contains functions to customize the Admin experience
 *
 * @package      ucsc
 * @since        0.1.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Remove Link Manager from Dash Sidebar
 *
 * @return void
 * Removes deprecated link manager from dashboard sidebar
 * @package
 * @since
 * @author UC Santa Cruz
 * @license GNU General Public License 2.0+
 */
function ucsc_custom_functionality_customize_dash() {

		remove_menu_page( 'link-manager.php' );

}
add_action( 'admin_menu', 'ucsc_custom_functionality_customize_dash' );

/**
 * Remove items from Admin Bar
 *
 * @param mixed $wp_admin_bar
 * @return void
 * Removes unwanted items from the WP Admin bar
 * @package
 * @since
 * @author UC Santa Cruz
 * @license GNU General Public License 2.0+
 */
function ucsc_custom_functionality_remove_from_admin_bar( $wp_admin_bar ) {
	if ( ! current_user_can( 'edit_themes' ) ) {
		$wp_admin_bar->remove_node( 'customize' );
		$wp_admin_bar->remove_node( 'site-editor' );
	}
}
add_action( 'admin_bar_menu', 'ucsc_custom_functionality_remove_from_admin_bar', 999 );


/**
 * Remove items from Dashboard panel
 *
 * @return void
 * Remove left-hand dashboard items, currently only removes Customize
 * @package
 * @since
 * @author UC Santa Cruz
 * @license GNU General Public License 2.0+
 */
function ucsc_custom_functionality_remove_from_dash_panel() {
	global $submenu;
	foreach ( $submenu as $name => $items ) {
		if ( $name === 'themes.php' ) {
			foreach ( $items as $i => $data ) {
				if ( in_array( 'customize', $data, true ) ) {
					unset( $submenu[ $name ][ $i ] );

					return;
				}
			}
		}
	}
}

add_action( 'admin_menu', 'ucsc_custom_functionality_remove_from_dash_panel' );
