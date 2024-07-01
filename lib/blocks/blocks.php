<?php

add_action( 'init', 'ucsc_register_custom_blocks' );

function ucsc_register_custom_blocks(){
	// Register custom blocks from Advanced Custom Fields
	register_block_type( UCSC_DIR . '/lib/blocks/article-source' );
}

function ucsc_article_source_block( $block ) {
	$id = 'article-source-' . $block['id'];
	if( !empty($block['anchor']) ) {
		$id = $block['anchor'];
	}
	// Create Class attribute allowing custom "className" and "align" values
	$className = 'article-source';
	if( !empty($block['className']) ) {
		$className .= ' ' . $block['className'];
	}
	if(!empty($block['align']) ) {
		$className .= ' align' . $block['align'];
	}
	// Get the post ID
	$post_id = get_the_ID();

	// Get the "Article Source" ACF field
	$article_source = get_field('article_source', $post_id);

	// Display the article source
	?>
	<p id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>"><?php echo $article_source; ?></p><?php
}