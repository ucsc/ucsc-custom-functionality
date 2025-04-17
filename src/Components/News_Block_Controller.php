<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Request\News_Request;

class News_Block_Controller {

	public const POSTS    = 'news_posts';
	public const PER_PAGE = 9;
	private const CACHE_EXPIRY = MINUTE_IN_SECONDS * 20;

	protected array $block;
	private string $taxonomy;
	private array $taxonomy_ids;
	private bool $hide_excerpt;
	private bool $hide_author;
	private bool $hide_image;
	private bool $hide_date;
	private bool $hide_tags;
	private bool $hide_category;
	private string $title;
	private string $description;
	private string $layout;
	private array|string $more_news_link;
	private int $posts_per_page;

	public function __construct($block) {
		$this->block          = (array) $block;
		$this->title          = get_field( News_Block::TITLE ) ?? '';
		$this->description    = get_field( News_Block::DESCRIPTION ) ?? '';
		$this->layout         = get_field( News_Block::LAYOUT ) ?? News_Block::LAYOUT_CENTRE;
		$this->more_news_link = get_field( News_Block::MORE_NEWS_LINK ) ?? [];
		$this->taxonomy       = get_field( News_Block::TAXONOMIES ) ?? '';
		$this->taxonomy_ids   = get_field( News_Block::TAX_ITEMS ) ?? [];
		$this->hide_excerpt   = (bool) get_field( News_Block::HIDE_EXCERPT );
		$this->hide_author    = (bool) get_field( News_Block::HIDE_AUTHOR );
		$this->hide_image     = (bool) get_field( News_Block::HIDE_IMAGE );
		$this->hide_date      = (bool) get_field( News_Block::HIDE_DATE );
		$this->hide_tags      = (bool) get_field( News_Block::HIDE_TAGS );
		$this->hide_category  = (bool) get_field( News_Block::HIDE_CATEGORY );
		$this->posts_per_page = (int) get_field( 'posts_per_page' ) ?? self::PER_PAGE;
	}

	public function get_title(): string {
		return $this->title;
	}

	public function get_description(): string {
		return $this->description;
	}
	
	public function get_alignment(): string {
		return $this->layout !== News_Block::LAYOUT_CENTRE ? ' align-header-left' : '';
	}

	public function get_more_news_link(): array {
		$link = [];

		if ( ! empty( $this->more_news_link['url'] ) ) {
			$link['url']    = $this->more_news_link['url'];
			$link['title']  = $this->more_news_link['title'] ?? __( 'More News', 'ucsc' );
			$link['target'] = $this->more_news_link['target'] ?? '';
		}

		return $link;
	}

	public function build_srcset(array $sizes = []): string {
		if ( empty( $sizes ) ) {
			return '';
		}

		$urls = [];
		foreach ( $sizes as $size ) {
			$urls[] = $size['source_url'] . ' ' . $size['width'] . 'w ' . $size['height'] . 'h';
		}

		return implode( ', ', $urls );
	}

	public function get_items(): array {
		if (empty($this->taxonomy_ids) || empty($this->taxonomy)) {
			return [];
		}

		$response = get_transient($this->get_cache_key());

		if (empty($response)) {
			// Fetch data using the constant PER_PAGE for the API request
			$response = (new News_Request())->request(News_Request::POSTS_ENDPOINT, [
				'per_page'      => self::PER_PAGE, // Use the constant here
				$this->taxonomy => implode(',', $this->taxonomy_ids),
			]);
		}

		if (empty($response)) {
			return [];
		}

		$items = [];

		foreach ($response as $item) {
			$items[] = [
				'title'        => $item['title']['rendered'] ?? '',
				'excerpt'      => !$this->hide_excerpt ? $item['excerpt']['rendered'] ?? '' : '',
				'permalink'    => $item['link'] ?? '',
				'image'        => !$this->hide_image ? $this->get_item_attachment($item) : [],
				'raw_date'     => !$this->hide_date ? $item['date'] : '',
				'publish_date' => !$this->hide_date ? wp_date(get_option('date_format', 'F j, Y'), strtotime($item['date'])) : '',
				'authors'      => !$this->hide_author ? $this->get_authors($item) : '',
				'tags'         => !$this->hide_tags ? $this->get_taxonomies($item, true) : [],
				'categories'   => !$this->hide_category ? $this->get_taxonomies($item) : [],
			];
		}

		set_transient($this->get_cache_key(), $response, self::CACHE_EXPIRY);

		return array_slice($items, 0, $this->posts_per_page);
	}

	protected function get_cache_key(string $prefix = ''): string {
		if ( ! empty( $prefix ) ) {
			return sprintf( '%s_%s_%s', $prefix, self::POSTS, implode( '_', $this->taxonomy_ids ) );
		}

		return sprintf( '%s_%s', self::POSTS, implode( '_', $this->taxonomy_ids ) );
	}

	protected function get_item_attachment(array $item): array {
		if ( ! isset( $item['featured_media'] ) || $item['featured_media'] <= 0 ) {
			return [];
		}

		$media = get_transient( $this->get_cache_key( 'attachment_' . $item['id'] ) );

		if ( empty( $media ) ) {
			$media = (new News_Request())->request( News_Request::ENDPOINT_BASE . 'media/' . $item['featured_media'] );
		}

		if ( empty( $media ) ) {
			return [];
		}

		set_transient( $this->get_cache_key( 'attachment_' . $item['id'] ), $media, self::CACHE_EXPIRY );

		return [
			'raw_url'    => $media['guid']['rendered'] ?? '',
			'width'      => $media['media_details']['width'] ?? 0,
			'height'     => $media['media_details']['height'] ?? 0,
			'image_meta' => $media['media_details']['image_meta'] ?? [],
			'sizes'      => $media['media_details']['sizes'] ?? [],
		];
	}

	protected function get_authors(array $item): array {
	if (empty($item['coauthors'])) {
		return [];
	}

	$authors = [];

	foreach ($item['coauthors'] as $author) {
		$user = get_transient($this->get_cache_key('coauthor_' . $author));
		if (empty($user)) {
			$user = (new News_Request())->request(News_Request::ENDPOINT_BASE . 'coauthors/' . $author);
		}

		if (empty($user)) {
			continue;
		}

		set_transient($this->get_cache_key('coauthor_' . $author), $user, self::CACHE_EXPIRY);
		$authors[] = $user['name'];
	}

	return $authors;
}

	protected function get_taxonomies(array $item, bool $is_tag = false) {
		$categories = [];

		if ( empty( $item[ $this->taxonomy ] ) ) {
			return [];
		}
		
		$taxonomy = $is_tag ? 'tags' : $this->taxonomy;

		$endpoint = News_Request::ENDPOINT_BASE . $taxonomy;

		$items = get_transient( $this->get_cache_key( $taxonomy . '_' . $item['id'] ) );
		if ( empty( $items ) ) {
			$items = ( new News_Request() )->request($endpoint, [
				'post'     => $item['id'],
				'per_page' => 3,
			] );
		}

		if ( empty( $items ) ) {
			return [];
		}

		set_transient( $this->get_cache_key( $taxonomy . '_' . $item['id'] ), $items, self::CACHE_EXPIRY );

		foreach ( $items as $category ) {
			$categories[] = $category['name'];
		}

		return $categories;
	}

}
