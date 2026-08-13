<?php
/**
 * Google Analytics and Tag Manager
 *
 * This file contains the functions necessary to add the UC Santa Cruz Google
 * Analytics and Tag Manager snippets to the site.
 *
 * The container ID is hardcoded, so every site sharing this plugin reports
 * into the same container; making it configurable is tracked in #113.
 *
 * @package      ucsc
 * @since        0.1.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

add_action( 'wp_head', 'ucsc_google_tag_manager_head', 1 );

add_action( 'wp_body_open', 'ucsc_google_tag_manager_body' );

/**
 * Print the Google Tag Manager snippet in the document head.
 *
 * Hooked early on wp_head so the container loads before other tracking.
 *
 * @return void
 */
function ucsc_google_tag_manager_head() {
	?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5RFHNC');</script>
<!-- End Google Tag Manager -->
	<?php
}

/**
 * Print the Google Tag Manager noscript fallback after the opening body tag.
 *
 * Requires theme support for wp_body_open.
 *
 * @return void
 */
function ucsc_google_tag_manager_body() {
	?>
	<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5RFHNC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<?php
}