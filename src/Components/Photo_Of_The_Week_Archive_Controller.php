<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Components\Traits\With_Image_Size;
use UCSC\Blocks\Object_Meta\Photo_Of_The_Week_Meta;
use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;

class Photo_Of_The_Week_Archive_Controller {

	use With_Image_Size;

	public function get_query( $paged ): \WP_Query {
		return new \WP_Query([
			'post_type'      => Photo_Of_The_Week::NAME,
			'post_status'    => 'publish',
			'posts_per_page' => get_option( 'posts_per_page' ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'paged'          => $paged,
		]);
	}

	public function get_image() {
		$image = get_field( Photo_Of_The_Week_Meta::IMAGE, get_the_ID() );

		if ( empty( $image ) || $image['ID'] < 1 ) {
			return '';
		}

		$image_meta = wp_get_attachment_metadata( $image['ID'] );

		$image_data = array_merge( [ 'id'  => $image['ID'], 'url' => $image['url'] ], $image_meta );

		return sprintf(
			'<img src="%s" srcset="%s" alt="%s" class="photo-of-the-week__image" />',
			$image['url'],
			$this->build_srcset( $image_data ),
			get_the_title( get_the_ID() )
		);
	}

	public function get_pagination( \WP_Query $query, int $paged ) {
		return paginate_links(
			[
				'total'     => $query->max_num_pages,
				'current'   => $paged,
				'format'    => 'page/%#%',
				'base'      => get_pagenum_link( 1 ) . '%_%',
				'prev_text' => __( '&lt;' ),
				'next_text' => __( '&gt;' ),
			]
		);
	}

}
