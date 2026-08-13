<?php
/**
 * News block controller.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\News_Block;
use UCSC\Blocks\Request\News_Request;

/**
 * Prepares the News block's posts for rendering.
 *
 * The only controller that sources its content remotely: posts, media,
 * authors and terms are all fetched from the news site over REST rather than
 * queried locally. Everything is cached in transients for 20 minutes.
 *
 * Note: a cold render issues one request for the posts and then a further
 * request per featured image, per author and per taxonomy, which can mean
 * well over a dozen sequential blocking calls. Batching these via _embed is
 * tracked in #107.
 */
class News_Block_Controller {

	/**
	 * Transient key prefix for cached responses.
	 *
	 * @var string
	 */
	public const POSTS = 'news_posts';
	/**
	 * Posts requested from the API.
	 *
	 * Fixed at the maximum the block offers; the editor's chosen count is
	 * applied by slicing the result rather than by narrowing the request.
	 *
	 * @var int
	 */
	public const PER_PAGE = 9;
	/**
	 * How long fetched data stays cached.
	 *
	 * @var int
	 */
	private const CACHE_EXPIRY = MINUTE_IN_SECONDS * 20;
	/**
	 * Author used when a post has no coauthors.
	 *
	 * A remote author ID on the news site, hardcoded here.
	 *
	 * @var int
	 */
	private const DEFAULT_AUTHOR_ID = 11;

	/**
	 * The block instance passed to the render callback.
	 *
	 * @var array
	 */
	protected array $block;
	/**
	 * REST base of the taxonomy being queried.
	 *
	 * @var string
	 */
	private string $taxonomy;
	/**
	 * Remote term IDs to filter posts by.
	 *
	 * @var int[]
	 */
	private array $taxonomy_ids;
	/**
	 * Whether to omit the excerpt.
	 *
	 * @var bool
	 */
	private bool $hide_excerpt;
	/**
	 * Whether to omit the author.
	 *
	 * @var bool
	 */
	private bool $hide_author;
	/**
	 * Whether to omit the featured image.
	 *
	 * @var bool
	 */
	private bool $hide_image;
	/**
	 * Whether to omit the published date.
	 *
	 * @var bool
	 */
	private bool $hide_date;
	/**
	 * Whether to omit tags.
	 *
	 * @var bool
	 */
	private bool $hide_tags;
	/**
	 * Whether to omit the category.
	 *
	 * @var bool
	 */
	private bool $hide_category;
	/**
	 * Heading shown above the posts.
	 *
	 * @var string
	 */
	private string $title;
	/**
	 * Description shown beneath the heading.
	 *
	 * @var string
	 */
	private string $description;
	/**
	 * Header alignment.
	 *
	 * @var string
	 */
	private string $layout;
	/**
	 * The optional "more news" link.
	 *
	 * @var array|string
	 */
	private array|string $more_news_link;
	/**
	 * How many posts to render.
	 *
	 * Blocks saved before this field existed resolve to 0, which renders an
	 * empty block; tracked in #106.
	 *
	 * @var int
	 */
	private int $posts_per_page;

	/**
	 * Read every saved field value into typed properties.
	 *
	 * @param mixed $block The block instance supplied by the render callback.
	 *
	 * @return void
	 */
	public function __construct( $block ) {
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

	/**
	 * The block heading.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * The block description.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Header alignment modifier class, empty when centred.
	 *
	 * @return string
	 */
	public function get_alignment(): string {
		return $this->layout !== News_Block::LAYOUT_CENTRE ? ' align-header-left' : '';
	}

	/**
	 * The "more news" link, or an empty array when unset.
	 *
	 * Falls back to a default title when a URL was given without one.
	 *
	 * @return array
	 */
	public function get_more_news_link(): array {
		$link = [];

		if ( ! empty( $this->more_news_link['url'] ) ) {
			$link['url']    = $this->more_news_link['url'];
			$link['title']  = $this->more_news_link['title'] ?? __( 'More News', 'ucsc' );
			$link['target'] = $this->more_news_link['target'] ?? '';
		}

		return $link;
	}

	/**
	 * Build a srcset from the sizes in a remote media payload.
	 *
	 * @param array $sizes Size descriptors from the REST media response.
	 *
	 * @return string
	 */
	public function build_srcset( array $sizes = [] ): string {
		if ( empty( $sizes ) ) {
			return '';
		}

		$urls = [];
		foreach ( $sizes as $size ) {
			$urls[] = $size['source_url'] . ' ' . $size['width'] . 'w ' . $size['height'] . 'h';
		}

		return implode( ', ', $urls );
	}

	/**
	 * Fetch and shape the posts for rendering.
	 *
	 * Returns an empty array unless both a taxonomy and at least one term are
	 * selected, so an unconfigured block renders nothing rather than an
	 * arbitrary post list. Hidden fields are omitted here rather than in the
	 * view, so the view does no conditional work.
	 *
	 * @return array
	 */
	public function get_items(): array {
		if ( empty( $this->taxonomy_ids ) || empty( $this->taxonomy ) ) {
			return [];
		}

		$response = get_transient( $this->get_cache_key() );

		if ( empty( $response ) ) {
			$response = ( new News_Request() )->request(
				News_Request::POSTS_ENDPOINT,
				[
					'per_page'      => self::PER_PAGE,
					$this->taxonomy => implode( ',', $this->taxonomy_ids ),
				]
			);
		}

		if ( empty( $response ) ) {
			return [];
		}

		$items = [];

		foreach ( $response as $item ) {
			$items[] = [
				'title'        => $item['title']['rendered'] ?? '',
				'excerpt'      => ! $this->hide_excerpt ? $item['excerpt']['rendered'] ?? '' : '',
				'permalink'    => $item['link'] ?? '',
				'image'        => ! $this->hide_image ? $this->get_item_attachment( $item ) : [],
				'raw_date'     => ! $this->hide_date ? $item['date'] : '',
				'publish_date' => ! $this->hide_date ? wp_date( get_option( 'date_format', 'F j, Y' ), strtotime( $item['date'] ) ) : '',
				'authors'      => ! $this->hide_author ? $this->get_authors( $item ) : '',
				'tags'         => ! $this->hide_tags ? $this->get_taxonomies( $item, true ) : [],
				'categories'   => ! $this->hide_category ? $this->get_taxonomies( $item ) : [],
			];
		}

		set_transient( $this->get_cache_key(), $response, self::CACHE_EXPIRY );

		return array_slice( $items, 0, $this->posts_per_page );
	}

	/**
	 * Compose a transient key.
	 *
	 * Note: every key embeds the selected term IDs, so the same attachment or
	 * author is cached separately for each block configuration that references
	 * it. Keying per-object caches by object ID alone is tracked in #107.
	 *
	 * @param string $prefix Optional prefix identifying what is cached.
	 *
	 * @return string
	 */
	protected function get_cache_key( string $prefix = '' ): string {
		if ( ! empty( $prefix ) ) {
			return sprintf( '%s_%s_%s', $prefix, self::POSTS, implode( '_', $this->taxonomy_ids ) );
		}

		return sprintf( '%s_%s', self::POSTS, implode( '_', $this->taxonomy_ids ) );
	}

	/**
	 * Fetch a post's featured image.
	 *
	 * @param array $item A post from the REST response.
	 *
	 * @return array
	 */
	protected function get_item_attachment( array $item ): array {
		if ( ! isset( $item['featured_media'] ) || $item['featured_media'] <= 0 ) {
			return [];
		}

		$media = get_transient( $this->get_cache_key( 'attachment_' . $item['id'] ) );

		if ( empty( $media ) ) {
			$media = ( new News_Request() )->request( News_Request::ENDPOINT_BASE . 'media/' . $item['featured_media'] );
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
			'alt'        => $media['alt_text'] ?? '',
		];
	}

	/**
	 * Resolve a post's author names.
	 *
	 * Uses Co-Authors Plus data when the post has it, and falls back to a
	 * single default author otherwise.
	 *
	 * @param array $item A post from the REST response.
	 *
	 * @return array
	 */
	protected function get_authors( array $item ): array {
		if ( ! empty( $item['coauthors'] ) ) {
			$authors = [];

			foreach ( $item['coauthors'] as $author ) {
				$user = get_transient( $this->get_cache_key( 'coauthor_' . $author ) );
				if ( empty( $user ) ) {
					$user = ( new News_Request() )->request( News_Request::ENDPOINT_BASE . 'coauthors/' . $author );
				}

				if ( empty( $user ) ) {
					continue;
				}

				set_transient( $this->get_cache_key( 'coauthor_' . $author ), $user, self::CACHE_EXPIRY );
				$authors[] = $user['title']['rendered'] ?? $user['name'];
			}

			return $authors;
		}

		$user = get_transient( $this->get_cache_key( 'coauthor_' . self::DEFAULT_AUTHOR_ID ) );
		if ( empty( $user ) ) {
			$user = ( new News_Request() )->request( News_Request::ENDPOINT_BASE . 'coauthors/' . self::DEFAULT_AUTHOR_ID );
		}

		if ( empty( $user ) ) {
			return [];
		}

		set_transient( $this->get_cache_key( 'coauthor_' . self::DEFAULT_AUTHOR_ID ), $user, self::CACHE_EXPIRY );

		return [ $user['title']['rendered'] ?? $user['name'] ];
	}

	/**
	 * Fetch up to three term names for a post.
	 *
	 * @param array $item   A post from the REST response.
	 * @param bool  $is_tag Read tags rather than the selected taxonomy.
	 *
	 * @return array
	 */
	protected function get_taxonomies( array $item, bool $is_tag = false ) {
		$categories = [];

		if ( empty( $item[ $this->taxonomy ] ) ) {
			return [];
		}

		$taxonomy = $is_tag ? 'tags' : $this->taxonomy;

		$endpoint = News_Request::ENDPOINT_BASE . $taxonomy;

		$items = get_transient( $this->get_cache_key( $taxonomy . '_' . $item['id'] ) );
		if ( empty( $items ) ) {
			$items = ( new News_Request() )->request(
				$endpoint,
				[
					'post'     => $item['id'],
					'per_page' => 3,
				]
			);
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
