<?php declare(strict_types=1);

namespace UCSC\Blocks\Query;

use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;

class Photo_Of_The_Week_Archive {

	public function handle_archive_path( $query_result ) {
		if ( ! is_post_type_archive( Photo_Of_The_Week::NAME ) ) {
			return $query_result;
		}

		$theme        = wp_get_theme();
		$block_source = 'custom';

		if ( file_exists( $theme->theme_root . '/' . $theme->stylesheet . '/templates/photo-of-the-week-archive.html' ) ) {
			$template_contents = file_get_contents( $theme->theme_root . '/' . $theme->stylesheet . '/templates/photo-of-the-week-archive.html' );
			$block_source      = 'theme';
		} else {
			$template_contents = file_get_contents( UCSC_DIR . '/src/views/templates/photo-of-the-week-archive.html' );
		}

		$new_block                 = new \WP_Block_Template();
		$new_block->type           = 'wp_template';
		$new_block->theme          = 'UCSC Custom Functionality';
		$new_block->slug           = 'photo-of-the-week-archive';
		$new_block->id             = 'ucsc-custom-functionality//photo-of-the-week-archive';
		$new_block->title          = esc_html__( 'Photo of the Week', 'ucsc' );
		$new_block->description    = esc_html__( 'Photo of the Week archive page', 'ucsc' );
		$new_block->source         = $block_source;
		$new_block->status         = 'publish';
		$new_block->has_theme_file = true;
		$new_block->is_custom      = true;
		$new_block->content        = $template_contents;
		$new_block->post_types     = [ Photo_Of_The_Week::NAME ];

		return [ $new_block ];
	}

}
