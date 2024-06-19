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
		add_options_page( 'UCSC Custom Functionality plugin page', 'UCSC Custom Functionality', 'manage_options', 'ucsc-custom-functionality-settings', 'ucsc_render_plugin_settings_page' );
	}
}
add_action( 'admin_menu', 'ucsc_add_settings_page' );


/** 
 * HTML output of Settings page 
 *
 * note: This page typically displays a form for displaying any settings options. 
 * It is not needed at this point.
 * https://developer.wordpress.org/plugins/settings/custom-settings-page/
 *
 */

if ( ! function_exists( 'ucsc_render_plugin_settings_page' ) ) {
	function ucsc_render_plugin_settings_page() {
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/ucsc-custom-functionality/plugin.php');
		?>
        <div class="custom-functionality-settings">
		<h1><?php echo $plugin_data['Name']; ?></h1>
		<h2>Version: <?php echo $plugin_data['Version']; ?> <a href="https://github.com/ucsc/ucsc-custom-functionality/releases">(release notes)</a></h2>
		<p><?php echo $plugin_data['Description']; ?></p>
		<h3>Features added by this plugin:</h3>
		<ul>
			<li>Google Analytics 4</li>
			<li>Site Improve</li>
			<li>Custom Shortcodes:
				<ul>
					<li><code>[site-search]</code>: Returns the HTML script tag to display <strong>Google Site Search</strong> results on a page</li>
					<li><code>[copyright]</code>: Returns copyright symbol and year (<?php echo do_shortcode('[copyright]')?>)</li>
					<li><code>[last-modified]</code>: Returns the "Last modified" date in the site footer</li>
				</ul>
			</li>
		</ul>
		</div>
		<?php
	}
}



