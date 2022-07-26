<?php

/**
 * Site Improve script
 * This file contains the functions necessary to add the UC Santa Cruz Site Improve script to the site.
 */

add_action( 'wp_footer', 'ucsc_site_improve_analytics' );

function ucsc_site_improve_analytics() {    ?>
<!-- Siteimprove -->
<script type="text/javascript">
/*<![CDATA[*/
(function() {
var sz = document.createElement('script'); sz.type = 'text/javascript'; sz.async = true;
sz.src = '//siteimproveanalytics.com/js/siteanalyze_8343.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(sz, s);
})();
/*]]>*/
</script>
<!-- end Siteimprove -->
	<?php
}