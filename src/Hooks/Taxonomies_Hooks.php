<?php
/**
 * Taxonomy field hooks for the block editor.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Hooks;

use UCSC\Blocks\Blocks\Contracts\Taxonomies;
use UCSC\Blocks\Blocks\Featured_News_Block;
use UCSC\Blocks\Traits\With_Get_Field_Key;

/**
 * Populates the taxonomy and term dropdowns on query-loop blocks.
 *
 * The choices cannot be declared statically in the field group because they
 * depend on what is registered on the site and on what the editor has already
 * selected, so they are filled in through ACF load filters at edit time.
 */
class Taxonomies_Hooks {

	use With_Get_Field_Key;

	/**
	 * Taxonomies hidden from the editor's taxonomy dropdown.
	 *
	 * These are WordPress internals — menus, templates, patterns — that are
	 * registered as taxonomies but are not editorial classifications.
	 *
	 * @var string[]
	 */
	public const RESTRICTED_TAXONOMIES = [
		'nav_menu',
		'link_category',
		'post_format',
		'wp_theme',
		'wp_template_part_area',
		'wp_pattern_category',
		'author',
	];

	/**
	 * Register the ACF load and search filters.
	 *
	 * The select-search filter is keyed by composed field key, so it only
	 * applies to the specific field it names.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_filter( 'acf/load_field/name=' . Taxonomies::TAXONOMIES, [ $this, 'load_taxonomies' ] );
		add_filter( 'acf/load_field/name=' . Taxonomies::TAX_ITEMS, [ $this, 'load_tax_items' ] );

		$query_blocks_search_key = [
			$this->get_field_key( Taxonomies::TAX_ITEMS, Featured_News_Block::NAME ),
		];

		foreach ( $query_blocks_search_key as $key ) {
			add_filter( 'acf/fields/select/query/key=' . $key, [ $this, 'load_search_tax_items' ] );
		}
	}

	/**
	 * Fill the taxonomy dropdown with the site's public taxonomies.
	 *
	 * @param array $field The ACF field definition.
	 *
	 * @return array The field, with its choices populated.
	 */
	public function load_taxonomies( array $field ): array {
		$taxonomies = get_taxonomies( [], false );

		if ( empty( $taxonomies ) ) {
			return $field;
		}

		/**
		 * Registered taxonomy objects, keyed by name.
		 *
		 * @var \WP_Taxonomy[] $taxonomies
		 */
		foreach ( $taxonomies as $key => $taxonomy ) {
			if ( in_array( $key, self::RESTRICTED_TAXONOMIES, true ) ) {
				continue;
			}

			$field['choices'][ $taxonomy->name ] = $taxonomy->label;
		}

		return $field;
	}

	/**
	 * Fill the term dropdown for the currently selected taxonomy.
	 *
	 * Falls back to 'category' when no taxonomy has been chosen yet, so the
	 * field is never empty on a freshly inserted block.
	 *
	 * @param array $field The ACF field definition.
	 *
	 * @return array The field, with its choices populated.
	 */
	public function load_tax_items( array $field ): array {
		$selected_tax = get_field( Taxonomies::TAXONOMIES );

		if ( empty( $selected_tax ) ) {
			$selected_tax = 'category';
		}

		$terms = get_terms(
			[
				'taxonomy'   => $selected_tax,
				'hide_empty' => true,
			]
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return $field;
		}

		/**
		 * Matching terms.
		 *
		 * @var \WP_Term[] $terms
		 */
		foreach ( $terms as $term ) {
			$field['choices'][ $term->term_id ] = $term->name;
		}

		return $field;
	}

	/**
	 * Answer the editor's AJAX term search.
	 *
	 * Runs only during an AJAX request; otherwise the value is passed straight
	 * through so ACF performs its normal query.
	 *
	 * Note: $_POST['taxonomy_selected'] is read without isset(), wp_unslash()
	 * or sanitisation. WordPress validates the taxonomy argument so the impact
	 * is limited, but the missing guards are tracked in #103.
	 *
	 * @param mixed $shortcut The response ACF will return, if short-circuited.
	 *
	 * @return mixed The response, with matching terms as results.
	 */
	public function load_search_tax_items( $shortcut ) {
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $shortcut;
		}

		$selected_taxonomy = $_POST['taxonomy_selected'];

		if ( empty( $selected_taxonomy ) ) {
			return $shortcut;
		}

		$terms = get_terms(
			[
				'taxonomy'   => $selected_taxonomy,
				'hide_empty' => false,
				'search'     => isset( $_POST['s'] ) ? sanitize_title_for_query( $_POST['s'] ) : '',
			]
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			$shortcut['results'] = [];

			return $shortcut;
		}

		/**
		 * Matching terms.
		 *
		 * @var \WP_Term[] $terms
		 */
		foreach ( $terms as $term ) {
			$shortcut['results'][] = [
				'id'   => $term->term_id,
				'text' => $term->name,
			];
		}

		return $shortcut;
	}
}
