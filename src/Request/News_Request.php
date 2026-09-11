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
	 * Request timeout, in seconds.
	 *
	 * @var int
	 */
	private const TIMEOUT = 10;

	/**
	 * Fetch a REST endpoint, optionally following pagination.
	 *
	 * With $with_pagination every page reported by X-WP-TotalPages is fetched
	 * and the results are concatenated. Any non-2xx response, transport error
	 * or undecodable body yields an empty array, so a failed fetch renders an
	 * empty block rather than an error.
	 *
	 * Each call starts from a clean slate: nothing is carried over from a
	 * previous call on the same instance.
	 *
	 * @param string $endpoint        Endpoint path, relative to the environment base URL.
	 * @param array  $args            Query arguments to append.
	 * @param bool   $with_pagination Whether to follow X-WP-TotalPages and fetch every page.
	 *
	 * @return array The decoded response body, or an empty array on failure.
	 */
	public function request( string $endpoint, array $args = [], bool $with_pagination = false ): array {
		$data = [];
		$page = 1;

		do {
			$page_args = $page > 1 ? array_merge( $args, [ 'page' => $page ] ) : $args;
			$response  = $this->fetch_page( $endpoint, $page_args );

			if ( null === $response ) {
				return [];
			}

			[ $body, $total_pages ] = $response;

			$data = array_merge( $data, $body );
			++$page;
		} while ( $with_pagination && $page <= $total_pages );

		return $data;
	}

	/**
	 * Fetch a single page of an endpoint.
	 *
	 * @param string $endpoint Endpoint path, relative to the environment base URL.
	 * @param array  $args     Query arguments to append.
	 *
	 * @return array{0: array, 1: int}|null The decoded body and the reported page count, or null on failure.
	 */
	private function fetch_page( string $endpoint, array $args ): ?array {
		try {
			$response = wp_remote_get(
				add_query_arg( $args, $this->get_endpoint_url( $endpoint ) ),
				[
					'timeout' => self::TIMEOUT,
					'headers' => [
						'Accept' => 'application/json',
					],
				]
			);

			$response_code = (int) wp_remote_retrieve_response_code( $response );

			if ( $response_code < 200 || $response_code >= 300 ) {
				return null;
			}

			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( ! is_array( $body ) ) {
				return null;
			}

			$total_pages = (int) wp_remote_retrieve_header( $response, 'X-WP-TotalPages' );

			return [ $body, max( 1, $total_pages ) ];
		} catch ( \Throwable $exception ) {
			return null;
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
