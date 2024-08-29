<?php declare(strict_types=1);

namespace UCSC\Blocks\Request;

class News_Request {

	public const TAXONOMY_ENDPOINT = 'wp-json/wp/v2/taxonomies';
	public const POSTS_ENDPOINT    = 'wp-json/wp/v2/posts';
	public const ENDPOINT_BASE     = 'wp-json/wp/v2/';

	private array $data = [];
	private int $page   = 1;

	public function request( string $endpoint, array $args = [], bool $with_pagination = false ): array {
		try {
			$url 		   = add_query_arg( $args, $this->get_endpoint_url( $endpoint ) );
			$response 	   = wp_remote_get( $url, [
				'headers' => [
					'Accept' => 'application/json',
				],
			] );
			$response_code = wp_remote_retrieve_response_code( $response );
			$total_pages   = (int) wp_remote_retrieve_header( $response, 'X-Wp-Totalpages' );
			if ( empty( $response_code ) || ! ( $response_code >= 200 && $response_code < 300 ) ) {
				return [];
			}

			$this->data = array_merge( $this->data, json_decode( wp_remote_retrieve_body( $response ), true ) );

			if ( ! $with_pagination || $total_pages <= 1 || $this->page >= $total_pages ) {
				return $this->data;
			}

			$this->page++;

			return $this->request( $endpoint, array_merge( $args, [
				'page' => $this->page,
			] ), true );
		} catch ( \Throwable $exception ) {
			return [];
		}
	}

	protected function get_endpoint_url( string $endpoint ): string {
		$env = ! defined( 'WP_ENVIRONMENT_TYPE' ) ? 'dev' : WP_ENVIRONMENT_TYPE;

		switch ( $env ) {
			case 'test':
				$base_url = 'https://test-news-ucsc.pantheonsite.io/';
				break;
			case 'live':
				$base_url = 'https://news.ucsc.edu/';
				break;
			default:
				$base_url = 'https://dev-news-ucsc.pantheonsite.io/';
				break;
		}

		return sprintf( '%s%s', $base_url, $endpoint );
	}

}
