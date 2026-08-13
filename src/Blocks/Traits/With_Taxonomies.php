<?php
/**
 * Shared taxonomy field definitions.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks\Traits;

/**
 * Supplies the taxonomy and term picker fields for the Taxonomies contract.
 *
 * Both fields ship with empty choices: they are populated at edit time by the
 * ACF load filters in src/Hooks/, which is why their names must match the
 * contract's constants exactly.
 */
trait With_Taxonomies {

	/**
	 * The taxonomy selector field.
	 *
	 * @param string $name The owning group name, used to compose the field key.
	 *
	 * @return array
	 */
	public function get_taxonomies_list( string $name ): array {
		return [
			'key'           => $this->get_field_key( self::TAXONOMIES, $name ),
			'label'         => esc_html__( 'Type of taxonomy', 'ucsc' ),
			'name'          => self::TAXONOMIES,
			'type'          => 'select',
			'choices'       => [],
			'ui'            => 1,
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select a taxonomy to query.', 'ucsc' ),
		];
	}

	/**
	 * The term selector field.
	 *
	 * AJAX-backed, so terms are searched remotely rather than rendered as a
	 * full list up front.
	 *
	 * @param string $name The owning group name, used to compose the field key.
	 *
	 * @return array
	 */
	public function get_taxonomies_items( string $name ): array {
		return [
			'key'           => $this->get_field_key( self::TAX_ITEMS, $name ),
			'label'         => esc_html__( 'Taxonomy terms', 'ucsc' ),
			'name'          => self::TAX_ITEMS,
			'type'          => 'select',
			'multiple'      => 0,
			'ui'            => 1,
			'ajax'          => 1,
			'choices'       => [],
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select the taxonomy term(s) to query.', 'ucsc' ),
		];
	}
}
