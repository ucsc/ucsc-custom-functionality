<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks\Traits;

trait With_Taxonomies {

	public function get_taxonomies_list( string $name ): array {
		return [
			'key'           => $this->get_field_key( self::TAXONOMIES, $name ),
			'label'         => esc_html__( 'Type of taxonomy', 'ucsc' ),
			'name'          => self::TAXONOMIES,
			'type'          => 'select',
			'choices'       => [],
			'ui'       		=> 1,
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select a taxonomy to query.', 'ucsc' ),
		];
	}

	public function get_taxonomies_items( string $name ): array {
		return [
			'key'           => $this->get_field_key( self::TAX_ITEMS, $name ),
			'label'         => esc_html__( 'Taxonomy terms', 'ucsc' ),
			'name'          => self::TAX_ITEMS,
			'type'          => 'select',
			'multiple'      => 0,
			'ui'      		=> 1,
			'ajax'			=> 1,
			'choices'       => [],
			'return_format' => 'value',
			'instructions'  => esc_html__( 'Select the taxonomy term(s) to query.', 'ucsc' ),
		];
	}

}
