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
