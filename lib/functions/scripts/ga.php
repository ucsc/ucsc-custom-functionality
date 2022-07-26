<?php

/**
 * Google Analytics and Tag Manager

 * This file contains the functions necessary to add the UC Santa Cruz Google Analytics and Tag Manager snippets to the site.
 */


add_action( 'wp_head', 'ucsc_google_tag_manager_head', 1 );

add_action( 'wp_body_open', 'ucsc_google_tag_manager_body' );

function ucsc_google_tag_manager_head() {   ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5RFHNC');</script>
<!-- End Google Tag Manager -->
	<?php
}

function ucsc_google_tag_manager_body() {
	?>
  <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5RFHNC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<?php

}