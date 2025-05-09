<?php
/**
 * Additional Scripts
 *
 * This file contains functions to customize the Admin experience.
 *
 * @package      ucsc
 * @since        0.1.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Disable XMLRPC.
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/disable-xmlrpc.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/disable-xmlrpc.php';
}
// Add Google TagManager.
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/ga.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/ga.php';
}

// Add SiteImprove script.
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/site-improve.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/site-improve.php';
}

/**
 * Add group block variation for aligned images
 *
 * @since 1.8.5
 * @author UCSC
 * @link  https://developer.wordpress.org/news/2024/08/how-to-extend-a-wordpress-block/#adding-custom-functionality
 */
function ucsc_cf_enqueue_block_variations()
{
    wp_enqueue_script(
        'ucsc-cf-enqueue-block-variations',
        UCSC_PLUGIN_URL . 'assets/js/variations.js',
        array('wp-blocks', 'wp-dom-ready'),
        wp_get_theme()->get('Version'),
        false
    );
}
add_action('enqueue_block_editor_assets', 'ucsc_cf_enqueue_block_variations');
