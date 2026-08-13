<?php
/**
 * Site Improve script
 *
 * This file contains the functions necessary to add the UC Santa Cruz Site
 * Improve script to the site.
 *
 * The analytics ID is hardcoded, so every site sharing this plugin reports
 * into the same account; making it configurable is tracked in #113.
 *
 * @package      ucsc
 * @since        0.1.0
 * @link         https://github.com/ucsc/ucsc-custom-functionality.git
 * @author       UC Santa Cruz
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

add_action( 'wp_footer', 'ucsc_site_improve_analytics' );

/**
 * Print the Site Improve analytics snippet in the footer.
 *
 * @return void
 */
function ucsc_site_improve_analytics() {
	?>
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