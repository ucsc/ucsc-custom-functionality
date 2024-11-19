<?php declare(strict_types=1);

namespace UCSC\Blocks\Hooks;

use UCSC\Blocks\Blocks\Contracts\Taxonomies;
use UCSC\Blocks\Blocks\Featured_News_Block;
use UCSC\Blocks\Traits\With_Get_Field_Key;

class Taxonomies_Hooks {
	
	use With_Get_Field_Key;
    
    public const RESTRICTED_TAXONOMIES = [
        'nav_menu',
        'link_category',
        'post_format',
        'wp_theme',
        'wp_template_part_area',
        'wp_pattern_category',
        'post_tag',
        'author',
    ];

	public function hooks(): void
    {
        add_filter('acf/load_field/name=' . Taxonomies::TAXONOMIES, [$this, 'load_taxonomies']);
        add_filter('acf/load_field/name=' . Taxonomies::TAX_ITEMS, [$this, 'load_tax_items']);

        $query_blocks_search_key = [
            $this->get_field_key(Taxonomies::TAX_ITEMS, Featured_News_Block::NAME),
        ];

        foreach ($query_blocks_search_key as $key) {
            add_filter('acf/fields/select/query/key=' . $key, [$this, 'load_search_tax_items']);
        }
    }
	
	public function load_taxonomies( array $field ): array {
		$taxonomies = get_taxonomies( [], false );
       
		if ( empty( $taxonomies ) ) {
			return $field;
		}

		/**
		 * @var \WP_Taxonomy[] $taxonomies
		 */
		foreach ( $taxonomies as $key => $taxonomy ) {
            if ( in_array( $key, self::RESTRICTED_TAXONOMIES ) ) {
                continue;
            }
            
			$field['choices'][ $taxonomy->name ] = $taxonomy->label;
		}

		return $field;
	}
	
	public function load_tax_items( array $field ): array {
		$selected_tax = get_field( Taxonomies::TAXONOMIES );

		if ( empty( $selected_tax ) ) {
			$selected_tax = 'category';
		}
		
		$terms = get_terms( [
			'taxonomy'   => $selected_tax,
			'hide_empty' => true,
		] );
      
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return $field;
		}

		/**
		 * @var \WP_Term[] $terms
		 */
		foreach ( $terms as $term ) {
			$field['choices'][ $term->term_id ] = $term->name;
		}

		return $field;
	}
	
	public function load_search_tax_items( $shortcut ) {
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $shortcut;
		}

		$selected_taxonomy = $_POST['taxonomy_selected'];

		if ( empty( $selected_taxonomy ) ) {
			return $shortcut;
		}

		$terms = get_terms( [
			'taxonomy'   => $selected_taxonomy,
			'hide_empty' => true,
		] );
        
       
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
            $shortcut['results'] = [];
            
			return $shortcut;
		}
        
		/**
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
