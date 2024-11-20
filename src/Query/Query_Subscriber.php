<?php declare(strict_types=1);

namespace UCSC\Blocks\Query;

use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;

class Query_Subscriber {
	
	public function init(): void {
		add_action( 'template_redirect', static function (): void {
			if ( ! is_singular( Photo_Of_The_Week::NAME ) ) {
				return;
			}

			( new Download_Photo() )->download_photo_of_the_week_single();
		}, 10, 0 );

		add_filter( 'get_block_templates', static function ( $query_result, $query, $template_type ) {
			return ( new Photo_Of_The_Week_Archive() )->handle_archive_path( $query_result );
		}, 10, 3 );
	}

}
