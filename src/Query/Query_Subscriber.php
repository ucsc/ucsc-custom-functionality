<?php
/**
 * Query and routing modifications.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Query;

use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;

/**
 * Adjusts front-end routing for news-site content types.
 */
class Query_Subscriber {

	/**
	 * Redirect single Photo of the Week views to the archive.
	 *
	 * The post type exists to populate the archive and the block; individual
	 * photos have no standalone page, so a direct hit is bounced rather than
	 * rendering an empty single template.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action(
			'template_redirect',
			static function (): void {
				if ( ! is_singular( Photo_Of_The_Week::NAME ) ) {
					return;
				}

				wp_safe_redirect( get_post_type_archive_link( Photo_Of_The_Week::NAME ) );
				exit;
			},
			10,
			0
		);
	}
}
