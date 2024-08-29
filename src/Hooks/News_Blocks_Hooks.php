<?php declare(strict_types=1);

namespace UCSC\Blocks\Hooks;

use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Request\News_Request;
use UCSC\Blocks\Traits\With_Get_Field_Key;

class News_Blocks_Hooks {
	
	use With_Get_Field_Key;
	
	private News_Request $request;
	
	public function __construct() {
		$this->request = new News_Request();
	}

	public function hooks(): void {
		add_filter( 'acf/load_field/name=' . News_Block::TAXONOMIES, [ $this, 'load_taxonomies' ] );
		add_filter( 'acf/load_field/name=' . News_Block::TAX_ITEMS, [ $this, 'load_tax_items' ] );
		add_filter( 'acf/fields/select/query/key=' . $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ), [ $this, 'load_search_tax_items' ] );
	}

	public function load_taxonomies( array $field ): array {
		$choices = get_transient( $this->get_field_key( News_Block::TAXONOMIES, News_Block::NAME ) );

		if ( ! empty( $choices ) ) {
			$field['choices'] = $choices;

			return $field;
		}

		$response = $this->request->request( News_Request::TAXONOMY_ENDPOINT, [ 'type' => 'post' ] );

		if ( empty( $response ) ) {
			return $field;
		}

		$choices = [];
		foreach ( $response as $taxonomy_name => $value ) {
			if ( ! isset( $value['rest_base'] ) || ! in_array( $taxonomy_name, News_Block::ALLOWED_TAX ) ) {
				continue;
			}

			$choices[ $value['rest_base'] ] = $value['name'];
		}

		set_transient( $this->get_field_key( News_Block::TAXONOMIES, News_Block::NAME ), $choices, MINUTE_IN_SECONDS * 20 );

		$field['choices'] = $choices;

		return $field;
	}

	public function load_tax_items( array $field ): array {
		$selected_tax = get_field( News_Block::TAXONOMIES );

		if ( empty( $selected_tax ) ) {
			$selected_tax = 'categories';
		}

		$choices = get_transient( $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ). '_' . $selected_tax );

		if ( ! empty( $choices ) ) {
			$field['choices'] = $choices;

			return $field;
		}

		$field['choices'] = $this->get_taxonomies_item_by_type( $selected_tax );

		set_transient( $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ). '_' . $selected_tax, $field['choices'], MINUTE_IN_SECONDS * 20 );

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

		$choices = get_transient( $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ). '_' . $selected_taxonomy . '_shortcat' );

		if ( ! empty( $choices ) ) {
			$shortcut['results'] = $choices;

			return $shortcut;
		}

		$choices = $this->get_taxonomies_item_by_type( $selected_taxonomy );

		if ( empty( $choices ) ) {
			return $shortcut;
		}

		$shortcut['results'] = [];

		foreach ( $choices as $id => $choice ) {
			if ( ! empty( $_POST['s'] ) && ! ( stripos( $choice, sanitize_title_for_query( $_POST['s'] ) ) !== false ) ) {
				continue;
			}

			$shortcut['results'][] = [
				'id'   => $id,
				'text' => $choice,
			];
		}

		set_transient( $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ). '_' . $selected_taxonomy . '_shortcat', $shortcut['results'], MINUTE_IN_SECONDS * 20 );

		return $shortcut;
	}

	protected function get_taxonomies_item_by_type( string $type = 'categories' ): array {
		$response = $this->request->request( News_Request::ENDPOINT_BASE . $type, [ 'per_page' => 100 ], true );

		if ( empty( $response ) ) {
			return [];
		}

		$choices = [];

		foreach ( $response as $entity ) {
			if ( ! isset( $entity['id'] ) ) {
				continue;
			}

			$choices[ $entity['id'] ] = $entity['name'];
		}

		return $choices;
	}

}
