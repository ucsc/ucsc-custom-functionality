<?php
/**
 * Title: Home Link
 * Slug: Display homepage link
 * Categories: theme
 */
?>
<!-- wp:paragraph -->
<p><a href="<?php echo get_bloginfo( 'url' ); ?>"><?php echo esc_html__( 'Home', 'ucsc' ); ?></a></p>
<!-- /wp:paragraph -->