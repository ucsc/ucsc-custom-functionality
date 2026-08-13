<?php
/**
 * Taxonomy field contract.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks\Contracts;

/**
 * Implemented by field groups offering a taxonomy and term picker.
 *
 * The field names are shared constants because the ACF load filters in
 * src/Hooks/ target them by name; a group that renames them silently loses
 * its editor dropdowns.
 */
interface Taxonomies {

	/**
	 * Field name of the taxonomy selector.
	 *
	 * @var string
	 */
	public const TAXONOMIES = 'taxonomies_list';

	/**
	 * Field name of the term selector.
	 *
	 * @var string
	 */
	public const TAX_ITEMS = 'taxonomy_list_items';

	/**
	 * The taxonomy selector field definition.
	 *
	 * Choices are filled in at edit time by the hooks, not declared here.
	 *
	 * @param string $name The owning group name.
	 *
	 * @return array
	 */
	public function get_taxonomies_list( string $name ): array;

	/**
	 * The term selector field definition.
	 *
	 * @param string $name The owning group name.
	 *
	 * @return array
	 */
	public function get_taxonomies_items( string $name ): array;
}
