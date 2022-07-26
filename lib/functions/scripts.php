<?php
/** Scripts */

//disable xmlrpc
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/disable-xmlrpc.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/disable-xmlrpc.php';
}
//UCSC Gogle Analytics
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/ga.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/ga.php';
}

//Security headers
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/security-headers.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/security-headers.php';
}

//Site Improve
if ( file_exists( UCSC_DIR . '/lib/functions/scripts/site-improve.php' ) ) {
	include_once UCSC_DIR . '/lib/functions/scripts/site-improve.php';
}
