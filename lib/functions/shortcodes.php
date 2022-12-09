<?php
/**
 * Shortcodes
 *
 * This file contains any shortcode functions
 *
 * @package      ucsc
 * @since        0.1.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


/**
 * Copyright shortcode
 * returns copyright symbol and current year
 */
function ucsc_custom_functionality_copyright() {
	$copyright = '&#169;';
	$year      = gmdate( 'Y' );
	return $copyright . $year;
}
add_shortcode( 'copyright', 'ucsc_custom_functionality_copyright' );

/**
 * Last Modified Callback Function
 *
 * Create a callback to build the last modified date
 *
 * @return String
 */
function ucsc_custom_functionality_last_modified_helper() {
	$ucsc_custom_functionality_modified_time = get_the_modified_time( 'U' );
	if ( $ucsc_custom_functionality_modified_time > 0 ) {
		return the_modified_time( 'M d, Y' );
	} else {
		return the_time( 'M d, Y' );
	}
}

/**
 * Return the last page modification in a readable format
 *
 * This is called by a short code `last-modified`. It looks at the
 * modified time and if that time is greater than zero it returns it
 * as a formatted date. Otherwise, it returns the date the page was created.
 *
 * @return String
 */
function ucsc_custom_functionality_last_modified() {
	ob_start();
	ucsc_custom_functionality_last_modified_helper();
	return ob_get_clean();
}
add_shortcode( 'last-modified', 'ucsc_custom_functionality_last_modified' );
