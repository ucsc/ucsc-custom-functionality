<?php declare(strict_types=1);

namespace UCSC\Blocks\Query;

use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;
use UCSC\Blocks\Template\Photo_Of_The_Week_Archive;

class Query_Subscriber {
	
	public function init(): void {
		add_action( 'template_redirect', static function (): void {
			if ( ! is_singular( Photo_Of_The_Week::NAME ) ) {
				return;
			}

			wp_safe_redirect( get_post_type_archive_link( Photo_Of_The_Week::NAME ) );
            exit;
		}, 10, 0 );
	}

}
