<?php declare(strict_types=1);

namespace UCSC\Blocks\Post_Types\Photo_Of_The_Week;

use UCSC\Blocks\Post_Types\Post_Types;

class Photo_Of_The_Week extends Post_Types {

	public const NAME = 'photo_of_the_week';

	public function get_args(): array {
		return [
			'labels'          => $this->get_labels(),
			'query_var'       => false,
			'map_meta_cap'    => true,
			'supports'        => [
				'title',
			],
			'show_in_menu'    => true,
			'public'          => true,
			'capability_type' => 'post',
			'show_in_rest'    => true,
			'has_archive'     => true,
			'rewrite'         => [
				'slug' => 'photo-of-the-week',
			],
		];
	}

	protected function get_labels(): array {
		return [
			'name'         => esc_html__( 'Photo of the Week', 'ucsc' ),
			'menu_name'    => esc_html__( 'Photo of the Week', 'ucsc' ),
			'add_new'      => esc_html__( 'Add Photo', 'ucsc' ),
			'add_new_item' => esc_html__( 'Add New Photo', 'ucsc' ),
			'edit_item'    => esc_html__( 'Edit Photo', 'ucsc' ),
			'new_item'     => esc_html__( 'Photo of the Week', 'ucsc' ),
			'all_items'    => esc_html__( 'Photos Of The Week', 'ucsc' ),
			'view_item'    => esc_html__( 'View Photo of the Week', 'ucsc' ),
			'search_items' => esc_html__( 'Search Photos Of The Week', 'ucsc' ),
		];
	}

}
