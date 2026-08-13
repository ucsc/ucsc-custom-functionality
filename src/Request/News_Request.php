<?php
/**
 * Remote REST client for the news site.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Request;

/**
 * Fetches content from the news site's REST API.
 *
 * This is the one place the plugin reaches outside the current install: the
 * News block renders posts pulled from news.ucsc.edu rather than the local
 * database. The base URL is chosen from the environment type, so a staging
 * install reads from the staging news site.
 */
class News_Request {

	/**
	 * Taxonomies endpoint, relative to the environment base URL.
	 *
	 * @var string
	 */
	public const TAXONOMY_ENDPOINT = 'wp-json/wp/v2/taxonomies';

	/**
	 * Posts endpoint, relative to the environment base URL.
	 *
	 * @var string
	 */
	public const POSTS_ENDPOINT = 'wp-json/wp/v2/posts';

	/**
	 * REST namespace prefix, for callers composing their own endpoint path.
	 *
	 * @var string
	 */
	public const ENDPOINT_BASE = 'wp-json/wp/v2/';

	/**
	 * Results accumulated across paginated requests.
	 *
	 * @var array
	 */
	private array $data = [];

	/**
	 * Page currently being fetched.
	 *
	 * @var int
	 */
	private int $page = 1;

	/**
	 * Fetch a REST endpoint, optionally following pagination.
	 *
	 * With $with_pagination the method recurses, accumulating every page into
	 * $data before returning. Any non-2xx response or thrown error yields an
	 * empty array, so a failed fetch renders an empty block rather than an
	 * error.
	 *
	 * Note: no explicit timeout is set, and the pagination recursion has no
	 * page cap, so a slow or very large response can stall a page render.
	 * Both are tracked in #107.
	 *
	 * @param string $endpoint        Endpoint path, relative to the environment base URL.
	 * @param array  $args            Query arguments to append.
	 * @param bool   $with_pagination Whether to follow X-WP-TotalPages and fetch every page.
	 *
	 * @return array The decoded response body, or an empty array on failure.
	 */
	public function request( string $endpoint, array $args = [], bool $with_pagination = false ): array {
		try {
			$url           = add_query_arg( $args, $this->get_endpoint_url( $endpoint ) );
			$response      = wp_remote_get(
				$url,
				[
					'headers' => [
						'Accept' => 'application/json',
					],
				]
			);
			$response_code = wp_remote_retrieve_response_code( $response );
			$total_pages   = (int) wp_remote_retrieve_header( $response, 'X-Wp-Totalpages' );
			if ( empty( $response_code ) || ! ( $response_code >= 200 && $response_code < 300 ) ) {
				return [];
			}

			$this->data = array_merge( $this->data, json_decode( wp_remote_retrieve_body( $response ), true ) );

			if ( ! $with_pagination || $total_pages <= 1 || $this->page >= $total_pages ) {
				return $this->data;
			}

			++$this->page;

			return $this->request(
				$endpoint,
				array_merge(
					$args,
					[
						'page' => $this->page,
					]
				),
				true
			);
		} catch ( \Throwable $exception ) {
			return [];
		}
	}

	/**
	 * Resolve an endpoint path against the environment's news site.
	 *
	 * Production and any unrecognised environment read from the live site;
	 * staging and development read from their Pantheon counterparts.
	 *
	 * @param string $endpoint Endpoint path, relative to the base URL.
	 *
	 * @return string The absolute endpoint URL.
	 */
	protected function get_endpoint_url( string $endpoint ): string {
		$env = wp_get_environment_type();

		switch ( $env ) {
			case 'production':
				$base_url = 'https://news.ucsc.edu/';
				break;
			case 'staging':
				$base_url = 'https://test-news-ucsc.pantheonsite.io/';
				break;
			case 'development':
				$base_url = 'https://dev-news-ucsc.pantheonsite.io/';
				break;
			default:
				$base_url = 'https://news.ucsc.edu/';
				break;
		}

		return sprintf( '%s%s', $base_url, $endpoint );
	}
}
