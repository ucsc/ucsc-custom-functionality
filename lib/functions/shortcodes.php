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
 * Google Site Search: [site-search]
 * returns the HTML script tag and properly configured <div> element
 * to display site search results on a WordPress page
 */
function ucsc_custom_functionality_google_search() {

    // Configuration params to be added to the output string
	$site_url = parse_url( get_site_url(), PHP_URL_HOST );
    $script_source = "https://cse.google.com/cse.js?cx=012090462228956765947:d0ywvq7bxee";
    
    // Search ucsc.edu if in site is in development
    if (preg_match("/wordpress(-dev)?/usmx", $site_url)) {
        $search_url = 'ucsc.edu';
    } else {
        $search_url = $site_url;
    }
    
    // Return the configured string for Google Search results to display on the page
    return sprintf('<script async src="%s"></script><div class="gcse-searchresults-only" data-queryParameterName="s" data-as_sitesearch="%s"></div>', $script_source, $search_url);
}
add_shortcode( 'site-search', 'ucsc_custom_functionality_google_search' );



/**
 * Copyright: [copyright]
 * returns copyright symbol and current year
 */
function ucsc_custom_functionality_copyright() {
	$copyright = '&#169;';
	$year      = gmdate( 'Y' );
	return $copyright . $year;
}
add_shortcode( 'copyright', 'ucsc_custom_functionality_copyright' );



/**
 * Last Modified: [last-modified]
 * This is called by a short code `last-modified`. It looks at the
 * modified time and if that time is greater than zero it returns it
 * as a formatted date. Otherwise, it returns the date the page was created.
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
 * Process the last-modified shortcode
 */
function ucsc_custom_functionality_last_modified() {
	ob_start();
	ucsc_custom_functionality_last_modified_helper();
	return ob_get_clean();
}
add_shortcode( 'last-modified', 'ucsc_custom_functionality_last_modified' );
