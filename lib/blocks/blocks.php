<?php

add_action( 'init', 'ucsc_register_custom_blocks');

function ucsc_register_custom_blocks(){
	// Register custom blocks from Advanced Custom Fields
	register_block_type( UCSC_DIR . '/lib/blocks/article-source' );
}

function ucsc_article_source_block( $block ) {

	// Get the post ID
	$post_id = get_the_ID();

	// Get the "Article Source" ACF field
	$article_source = get_field('article_source', $post_id);

	// Display the article source
	echo '<p>' . $article_source . '</p>';
}