<?php
/** Scripts */

// Disable XMLRPC
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/disable-xmlrpc.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/disable-xmlrpc.php';
}
// Add Google TagManager
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/ga.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/ga.php';
}

// Add SiteImprove script
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/site-improve.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/site-improve.php';
}
