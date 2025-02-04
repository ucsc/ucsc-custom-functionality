<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks\Contracts;

interface Taxonomies {

	public const TAXONOMIES = 'taxonomies_list';
	public const TAX_ITEMS  = 'taxonomy_list_items';

	public function get_taxonomies_list( string $name ): array;

	public function get_taxonomies_items( string $name ): array;
	
}
