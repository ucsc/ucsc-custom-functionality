<?php
/**
 * News block editor field hooks.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Hooks;

use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Request\News_Request;
use UCSC\Blocks\Traits\With_Get_Field_Key;
use UCSC\Blocks\Traits\With_Posted_Input;

/**
 * Populates the News block's taxonomy and term dropdowns from the news site.
 *
 * Unlike Taxonomies_Hooks, which reads local taxonomies, every choice here is
 * fetched over REST from the remote news site. Responses are cached in
 * transients for 20 minutes, keyed by composed field key — so when the editor
 * dropdowns look stale, those transients are what to delete.
 */
class News_Blocks_Hooks {

	use With_Get_Field_Key;
	use With_Posted_Input;

	/**
	 * Client for the news site's REST API.
	 *
	 * @var News_Request
	 */
	private News_Request $request;

	/**
	 * Set up the remote request client.
	 */
	public function __construct() {
		$this->request = new News_Request();
	}

	/**
	 * Register the ACF load and search filters for the News block.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_filter( 'acf/load_field/name=' . News_Block::TAXONOMIES, [ $this, 'load_taxonomies' ] );
		add_filter( 'acf/load_field/name=' . News_Block::TAX_ITEMS, [ $this, 'load_tax_items' ] );
		add_filter( 'acf/fields/select/query/key=' . $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ), [ $this, 'load_search_tax_items' ] );
	}

	/**
	 * Fill the taxonomy dropdown from the news site.
	 *
	 * @param array $field The ACF field definition.
	 *
	 * @return array The field, with its choices populated.
	 */
	public function load_taxonomies( array $field ): array {
		$choices = $this->get_taxonomy_choices();

		if ( ! empty( $choices ) ) {
			$field['choices'] = $choices;
		}

		return $field;
	}

	/**
	 * The remote taxonomies offered to the editor, keyed by REST base.
	 *
	 * Only taxonomies listed in News_Block::ALLOWED_TAX are included. Choices
	 * are keyed by REST base, since that is what the posts query needs, and the
	 * same keys are what the editor posts back as taxonomy_selected — so this
	 * list doubles as the allow-list for load_search_tax_items().
	 *
	 * @return array Taxonomy labels keyed by REST base, or an empty array on failure.
	 */
	protected function get_taxonomy_choices(): array {
		$transient_key = $this->get_field_key( News_Block::TAXONOMIES, News_Block::NAME );
		$choices       = get_transient( $transient_key );

		if ( ! empty( $choices ) && is_array( $choices ) ) {
			return $choices;
		}

		$response = $this->request->request( News_Request::TAXONOMY_ENDPOINT, [ 'type' => 'post' ] );

		if ( empty( $response ) ) {
			return [];
		}

		$choices = [];
		foreach ( $response as $taxonomy_name => $value ) {
			if ( ! isset( $value['rest_base'] ) || ! in_array( $taxonomy_name, News_Block::ALLOWED_TAX, true ) ) {
				continue;
			}

			$choices[ $value['rest_base'] ] = $value['name'];
		}

		set_transient( $transient_key, $choices, MINUTE_IN_SECONDS * 20 );

		return $choices;
	}

	/**
	 * Fill the term dropdown for the selected remote taxonomy.
	 *
	 * Falls back to 'categories' when nothing has been chosen yet.
	 *
	 * @param array $field The ACF field definition.
	 *
	 * @return array The field, with its choices populated.
	 */
	public function load_tax_items( array $field ): array {
		$selected_tax = get_field( News_Block::TAXONOMIES );

		if ( empty( $selected_tax ) ) {
			$selected_tax = 'categories';
		}

		$field['choices'] = $this->get_term_choices( $selected_tax );

		return $field;
	}

	/**
	 * Every term of a remote taxonomy, keyed by remote term ID.
	 *
	 * Cached for 20 minutes per taxonomy. Shared by the dropdown and the AJAX
	 * search, so the search filters this list in PHP rather than querying the
	 * news site per keystroke.
	 *
	 * @param string $rest_base The taxonomy's REST base.
	 *
	 * @return array Term names keyed by remote term ID, or an empty array on failure.
	 */
	protected function get_term_choices( string $rest_base ): array {
		$transient_key = $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ) . '_' . $rest_base;
		$choices       = get_transient( $transient_key );

		if ( ! empty( $choices ) && is_array( $choices ) ) {
			return $choices;
		}

		$choices = $this->get_taxonomies_item_by_type( $rest_base );

		set_transient( $transient_key, $choices, MINUTE_IN_SECONDS * 20 );

		return $choices;
	}

	/**
	 * Answer the editor's AJAX term search against the remote taxonomy.
	 *
	 * The posted taxonomy is accepted only if it is one of the REST bases this
	 * class offered in the taxonomy dropdown, because it ends up in both the
	 * outbound request path and a transient key.
	 *
	 * @param mixed $shortcut The response ACF will return, if short-circuited.
	 *
	 * @return mixed The response, with matching terms as results.
	 */
	public function load_search_tax_items( $shortcut ) {
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $shortcut;
		}

		$selected_taxonomy = $this->get_posted_taxonomy();

		if ( empty( $selected_taxonomy ) ) {
			return $shortcut;
		}

		$choices = $this->get_term_choices( $selected_taxonomy );

		if ( empty( $choices ) ) {
			return $shortcut;
		}

		$search = $this->get_posted_search();

		$shortcut['results'] = [];

		foreach ( $choices as $id => $choice ) {
			if ( '' !== $search && stripos( $choice, $search ) === false ) {
				continue;
			}

			$shortcut['results'][] = [
				'id'   => $id,
				'text' => $choice,
			];
		}

		return $shortcut;
	}

	/**
	 * The taxonomy REST base posted by the editor, if it is one we offered.
	 *
	 * @return string The validated REST base, or '' when absent or not allowed.
	 */
	protected function get_posted_taxonomy(): string {
		$selected_taxonomy = $this->get_posted_key( 'taxonomy_selected' );

		if ( '' === $selected_taxonomy || ! array_key_exists( $selected_taxonomy, $this->get_taxonomy_choices() ) ) {
			return '';
		}

		return $selected_taxonomy;
	}

	/**
	 * Fetch every term of a remote taxonomy, following pagination.
	 *
	 * @param string $type The taxonomy's REST base.
	 *
	 * @return array Term names keyed by remote term ID, or an empty array on failure.
	 */
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
