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
 * transients for 20 minutes — the taxonomy list under the taxonomies field
 * key, and each taxonomy's full, unfiltered term list under the terms field
 * key suffixed with the taxonomy's REST base. When the editor dropdowns look
 * stale, those transients are what to delete.
 */
class News_Blocks_Hooks {

	use With_Get_Field_Key;

	/**
	 * How long fetched choices stay cached.
	 *
	 * @var int
	 */
	private const CACHE_EXPIRY = MINUTE_IN_SECONDS * 20;

	/**
	 * Taxonomy assumed when a block has not chosen one yet.
	 *
	 * @var string
	 */
	private const DEFAULT_TAXONOMY = 'categories';

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
		add_filter( 'acf/prepare_field/key=' . $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ), [ $this, 'prepare_tax_items' ] );
		add_filter( 'acf/fields/select/query/key=' . $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ), [ $this, 'load_search_tax_items' ], 10, 2 );
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
	 * Label the block's selected terms when the sidebar form is rendered.
	 *
	 * The terms select is AJAX-backed, so it only needs choices for the values
	 * already saved; the rest arrive through load_search_tax_items(). This runs
	 * on acf/prepare_field rather than acf/load_field because ACF caches a
	 * loaded field for the whole request and may load it before the block's
	 * meta is in place, in which case get_field() cannot see the block's
	 * taxonomy yet. prepare_field runs per render, once the value is known.
	 *
	 * @param array $field The ACF field definition, with its value loaded.
	 *
	 * @return array The field, with choices covering its selected values.
	 */
	public function prepare_tax_items( array $field ): array {
		if ( empty( $field['value'] ) ) {
			return $field;
		}

		$selected_tax = get_field( News_Block::TAXONOMIES );

		if ( empty( $selected_tax ) || ! is_string( $selected_tax ) ) {
			$selected_tax = self::DEFAULT_TAXONOMY;
		}

		$field['choices'] = $this->get_term_choices( $selected_tax );

		return $field;
	}

	/**
	 * Answer the editor's AJAX term search against the remote taxonomy.
	 *
	 * The taxonomy comes from the request's `taxonomy_selected` value, which
	 * the editor script adds from the same block's taxonomy field. It must be
	 * one of the REST bases offered by load_taxonomies(). The full term list
	 * is cached; the search string is applied on every call.
	 *
	 * Always returns a complete response, even an empty one, so ACF never
	 * falls back to the field's own choices.
	 *
	 * @param mixed $shortcut The response ACF will return, if short-circuited.
	 * @param array $options  The AJAX query options ACF parsed from the request.
	 *
	 * @return array The response, with matching terms as results.
	 */
	public function load_search_tax_items( $shortcut, array $options = [] ): array {
		if ( ! $this->is_term_search_request() ) {
			return is_array( $shortcut ) ? $shortcut : [];
		}

		// Nonce is verified by ACF in acf_field_select::ajax_query() before this filter runs.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$selected_taxonomy = isset( $_POST['taxonomy_selected'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy_selected'] ) ) : '';
		$search            = isset( $options['s'] ) ? sanitize_text_field( wp_unslash( (string) $options['s'] ) ) : '';

		$results = [];

		if ( '' !== $selected_taxonomy && array_key_exists( $selected_taxonomy, $this->get_taxonomy_choices() ) ) {
			foreach ( $this->get_term_choices( $selected_taxonomy ) as $id => $name ) {
				if ( '' !== $search && false === stripos( $name, $search ) ) {
					continue;
				}

				$results[] = [
					'id'   => $id,
					'text' => $name,
				];
			}
		}

		return [ 'results' => $results ];
	}

	/**
	 * Whether the current request is ACF's AJAX select search.
	 *
	 * Block previews are also fetched over admin-ajax, so wp_doing_ajax()
	 * alone is not specific enough.
	 *
	 * @return bool
	 */
	protected function is_term_search_request(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- read-only routing check; ACF verifies the nonce.
		return wp_doing_ajax() && isset( $_POST['action'] ) && 'acf/fields/select/query' === $_POST['action'];
	}

	/**
	 * The remote taxonomies an editor may choose, keyed by REST base.
	 *
	 * Only taxonomies listed in News_Block::ALLOWED_TAX are offered. Choices
	 * are keyed by REST base, since that is what the posts query needs.
	 *
	 * @return array<string, string> Taxonomy labels keyed by REST base.
	 */
	protected function get_taxonomy_choices(): array {
		$transient = $this->get_field_key( News_Block::TAXONOMIES, News_Block::NAME );
		$choices   = get_transient( $transient );

		if ( is_array( $choices ) && ! empty( $choices ) ) {
			return $choices;
		}

		$response = $this->request->request( News_Request::TAXONOMY_ENDPOINT, [ 'type' => 'post' ] );

		if ( empty( $response ) ) {
			return [];
		}

		$choices = [];
		foreach ( $response as $taxonomy_name => $value ) {
			if ( ! isset( $value['rest_base'], $value['name'] ) || ! in_array( $taxonomy_name, News_Block::ALLOWED_TAX, true ) ) {
				continue;
			}

			// The REST API returns names HTML-encoded; decode so they are not escaped twice on output.
			$choices[ (string) $value['rest_base'] ] = html_entity_decode( (string) $value['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}

		if ( ! empty( $choices ) ) {
			set_transient( $transient, $choices, self::CACHE_EXPIRY );
		}

		return $choices;
	}

	/**
	 * Every term of a remote taxonomy, keyed by remote term ID.
	 *
	 * Served from a per-taxonomy transient when available. The cached list is
	 * never filtered, so a search never shortens what later callers see.
	 *
	 * @param string $rest_base The taxonomy's REST base.
	 *
	 * @return array<int, string> Term names keyed by remote term ID.
	 */
	protected function get_term_choices( string $rest_base ): array {
		$transient = $this->get_field_key( News_Block::TAX_ITEMS, News_Block::NAME ) . '_' . $rest_base;
		$choices   = get_transient( $transient );

		if ( is_array( $choices ) && ! empty( $choices ) ) {
			return $choices;
		}

		$choices = $this->get_taxonomies_item_by_type( $rest_base );

		if ( ! empty( $choices ) ) {
			set_transient( $transient, $choices, self::CACHE_EXPIRY );
		}

		return $choices;
	}

	/**
	 * Fetch every term of a remote taxonomy, following pagination.
	 *
	 * @param string $type The taxonomy's REST base.
	 *
	 * @return array<int, string> Term names keyed by remote term ID, or an empty array on failure.
	 */
	protected function get_taxonomies_item_by_type( string $type = self::DEFAULT_TAXONOMY ): array {
		$response = $this->request->request( News_Request::ENDPOINT_BASE . $type, [ 'per_page' => 100 ], true );

		if ( empty( $response ) ) {
			return [];
		}

		$choices = [];

		foreach ( $response as $entity ) {
			if ( ! isset( $entity['id'], $entity['name'] ) ) {
				continue;
			}

			// The REST API returns names HTML-encoded; decode so they are not escaped twice on output.
			$choices[ (int) $entity['id'] ] = html_entity_decode( (string) $entity['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}

		return $choices;
	}
}
