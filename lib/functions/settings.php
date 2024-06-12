<?php
/**
 * Add Plugin settings and info page
 *
 * This file contains functions to add a settings/info page below WordPress Settings menu
 *
 * @package      ucsc
 * @since        1.7.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/** Register new menu and page */

if ( ! function_exists( 'ucsc_add_settings_page' ) ) {
	function ucsc_add_settings_page() {
		add_options_page( 'UCSC Custom Functionality plugin page', 'UCSC Custom Functionality Info', 'manage_options', 'ucsc-custom-functionality-settings', 'ucsc_render_plugin_settings_page' );
	}
}
add_action( 'admin_menu', 'ucsc_add_settings_page' );


/** 
 * HTML output of Settings page 
 *
 * note: This page typically displays a form for displaying any settings options. 
 * It is not needed at this point
 *
 */

if ( ! function_exists( 'ucsc_render_plugin_settings_page' ) ) {
	function ucsc_render_plugin_settings_page() {
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/ucsc-custom-functionality/plugin.php');
		?>
		<h1>UCSC Custom Functionality Plugin</h1>
		<h2>Version: <?php echo $plugin_data['Version']; ?></h2>
		<p>Custom functionality for UCSC WordPress Websites.</p>
		<?php
	}
}



