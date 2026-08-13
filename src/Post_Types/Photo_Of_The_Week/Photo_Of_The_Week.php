<?php
/**
 * Photo of the Week post type.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Post_Types\Photo_Of_The_Week;

use UCSC\Blocks\Post_Types\Post_Types;

/**
 * The Photo of the Week custom post type. News sites only.
 *
 * Individual photos have no standalone page — Query_Subscriber redirects
 * single views to the archive — so the type exists to populate the archive
 * template and the Photos of the Week block.
 */
class Photo_Of_The_Week extends Post_Types {

	/**
	 * The post type slug.
	 *
	 * @var string
	 */
	public const NAME = 'photo_of_the_week';

	/**
	 * Arguments passed to register_post_type().
	 *
	 * Registers an archive under the 'photo-of-the-week' rewrite slug and
	 * supports only a title; the photograph and credit come from ACF meta.
	 *
	 * @return array
	 */
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

	/**
	 * Admin-facing labels for the post type.
	 *
	 * @return array
	 */
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
