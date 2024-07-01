<?php
/**
 * Post Types
 *
 * This file registers any custom post types
 *
 * @package   ucsc
 * @since     1.0.0
 * @link      https://github.com/ucsc/ucsc-custom-functionality.git
 * @author    UC Santa Cruz
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! function_exists( 'ucsc_register_media_coverage_post_type' ) ) {
	/**
	 * Create Media Coverage post type
	 *
	 * @since 1.0.0
	 * @link  http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	function ucsc_register_media_coverage_post_type()
	{
		$labels = array(
		'name' => 'Media Coverage',
		'singular_name' => 'Media Coverage Post',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Media Coverage',
		'edit_item' => 'Edit Media Coverage Post',
		'new_item' => 'New Media Coverage Post',
		'view_item' => 'View Media Coverage Post',
		'search_items' => 'Search Media Coverage Posts',
		'not_found' =>  'No Media Coverage Posts found',
		'not_found_in_trash' => 'No Media Coverage Posts found in trash',
		'parent_item_colon' => '',
		'menu_name' => 'Media Coverage',
		);

		$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_rest'       => true, // To use Gutenberg editor.
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_icon' => 'dashicons-admin-links',
		'menu_position' => 23,
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail'),
		'taxonomies' => array( 'post_tag', 'category')
		);

		register_post_type('media_coverage', $args);
	}
}

add_action('init', 'ucsc_register_media_coverage_post_type');

