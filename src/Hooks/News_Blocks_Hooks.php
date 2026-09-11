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

		$choices = get_transient( $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ) . '_' . $selected_tax );

		if ( ! empty( $choices ) ) {
			$field['choices'] = $choices;

			return $field;
		}

		$field['choices'] = $this->get_taxonomies_item_by_type( $selected_tax );

		set_transient( $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ) . '_' . $selected_tax, $field['choices'], MINUTE_IN_SECONDS * 20 );

		return $field;
	}

	/**
	 * Answer the editor's AJAX term search against the remote taxonomy.
	 *
	 * Filters the cached choice list in PHP rather than querying the news site
	 * per keystroke.
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

		$search = $this->get_posted_search();

		$transient_key = $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ) . '_' . $selected_taxonomy . '_shortcat';
		$choices       = get_transient( $transient_key );

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
			if ( '' !== $search && stripos( $choice, $search ) === false ) {
				continue;
			}

			$shortcut['results'][] = [
				'id'   => $id,
				'text' => $choice,
			];
		}

		set_transient( $transient_key, $shortcut['results'], MINUTE_IN_SECONDS * 20 );

		return $shortcut;
	}

	/**
	 * The taxonomy REST base posted by the editor, if it is one we offered.
	 *
	 * ACF verifies the AJAX nonce before this filter runs, so no nonce check
	 * is repeated here.
	 *
	 * @return string The validated REST base, or '' when absent or not allowed.
	 */
	protected function get_posted_taxonomy(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by ACF's AJAX handler before the acf/fields/select/query filter fires.
		if ( ! isset( $_POST['taxonomy_selected'] ) || ! is_string( $_POST['taxonomy_selected'] ) ) {
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- see above.
		$selected_taxonomy = sanitize_key( wp_unslash( $_POST['taxonomy_selected'] ) );

		if ( '' === $selected_taxonomy || ! array_key_exists( $selected_taxonomy, $this->get_taxonomy_choices() ) ) {
			return '';
		}

		return $selected_taxonomy;
	}

	/**
	 * The search string posted by the editor.
	 *
	 * @return string The sanitised search string, or '' when absent.
	 */
	protected function get_posted_search(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by ACF's AJAX handler before the acf/fields/select/query filter fires.
		if ( ! isset( $_POST['s'] ) || ! is_string( $_POST['s'] ) ) {
			return '';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- see above.
		return sanitize_text_field( wp_unslash( $_POST['s'] ) );
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
