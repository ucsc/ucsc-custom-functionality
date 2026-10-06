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
 * Featured images and terms (including Co-Authors Plus authors, which are
 * terms of the remote `author` taxonomy) are embedded in the posts response,
 * so a cold render is a single request. The one exception is a post with no
 * coauthors, which costs one cached request for the default author.
 */
class News_Block_Controller {

	/**
	 * Transient key prefix for cached responses.
	 *
	 * Changed whenever the shape of the cached posts response changes, so a
	 * response cached in the old shape is never read back as the new one.
	 *
	 * @var string
	 */
	public const POSTS = 'news_posts_embed';
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
	 * Resources embedded in the posts response.
	 *
	 * Always requested in full, whatever the hide flags, because the cached
	 * response is shared by every block with the same taxonomy selection.
	 *
	 * @var string
	 */
	private const EMBED = 'wp:featuredmedia,wp:term';
	/**
	 * Most terms shown per post, per taxonomy.
	 *
	 * @var int
	 */
	private const TERMS_PER_ITEM = 3;
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
		return News_Block::LAYOUT_CENTRE !== $this->layout ? ' align-header-left' : '';
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
					'_embed'        => self::EMBED,
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
	 * The key embeds both the taxonomy and the selected term IDs, since term
	 * IDs alone are not unique across taxonomies.
	 *
	 * Note: every key embeds the selection, so the default author is cached
	 * separately for each block configuration. Keying it by ID alone is
	 * tracked in #107.
	 *
	 * @param string $prefix Optional prefix identifying what is cached.
	 *
	 * @return string
	 */
	protected function get_cache_key( string $prefix = '' ): string {
		$selection = sprintf( '%s_%s_%s', self::POSTS, $this->taxonomy, implode( '_', $this->taxonomy_ids ) );

		if ( ! empty( $prefix ) ) {
			return sprintf( '%s_%s', $prefix, $selection );
		}

		return $selection;
	}

	/**
	 * Read a post's featured image from the embedded media.
	 *
	 * @param array $item A post from the REST response.
	 *
	 * @return array
	 */
	protected function get_item_attachment( array $item ): array {
		if ( ! isset( $item['featured_media'] ) || $item['featured_media'] <= 0 ) {
			return [];
		}

		$media = $item['_embedded']['wp:featuredmedia'][0] ?? [];

		// Media the news site will not expose embeds as an error object with no ID.
		if ( ! is_array( $media ) || empty( $media['id'] ) ) {
			return [];
		}

		// The embed context omits guid, so the file URL comes from source_url.
		return [
			'raw_url'    => $media['source_url'] ?? '',
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
	 * Co-Authors Plus authors are terms of the remote `author` taxonomy, and
	 * the post's `coauthors` IDs are their term IDs, so their names are read
	 * from the embedded terms in coauthor order. A post with no coauthors
	 * falls back to a single default author, fetched separately.
	 *
	 * @param array $item A post from the REST response.
	 *
	 * @return array
	 */
	protected function get_authors( array $item ): array {
		if ( ! empty( $item['coauthors'] ) ) {
			$terms   = $this->get_embedded_terms( $item );
			$authors = [];

			foreach ( (array) $item['coauthors'] as $author ) {
				if ( isset( $terms[ (int) $author ] ) ) {
					$authors[] = $terms[ (int) $author ]['name'];
				}
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
	 * Read up to three term names for a post from the embedded terms.
	 *
	 * The embedded terms arrive in the REST API's default name order, which
	 * is kept.
	 *
	 * @param array $item   A post from the REST response.
	 * @param bool  $is_tag Read tags rather than the selected taxonomy.
	 *
	 * @return array
	 */
	protected function get_taxonomies( array $item, bool $is_tag = false ) {
		$rest_base = $is_tag ? 'tags' : $this->taxonomy;

		if ( empty( $item[ $rest_base ] ) || ! is_array( $item[ $rest_base ] ) ) {
			return [];
		}

		$term_ids   = array_map( 'intval', $item[ $rest_base ] );
		$categories = [];

		foreach ( $this->get_embedded_terms( $item ) as $term_id => $term ) {
			if ( ! in_array( $term_id, $term_ids, true ) ) {
				continue;
			}

			// The REST API returns names HTML-encoded; decode so the view's esc_html() does not encode twice.
			$categories[] = html_entity_decode( (string) $term['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8' );

			if ( count( $categories ) >= self::TERMS_PER_ITEM ) {
				break;
			}
		}

		return $categories;
	}

	/**
	 * Every term embedded in a post, across all taxonomies, keyed by term ID.
	 *
	 * Term IDs are unique across taxonomies, so one flat map serves every
	 * lookup.
	 *
	 * @param array $item A post from the REST response.
	 *
	 * @return array<int, array> Embedded terms keyed by term ID.
	 */
	protected function get_embedded_terms( array $item ): array {
		$terms = [];

		foreach ( (array) ( $item['_embedded']['wp:term'] ?? [] ) as $taxonomy_terms ) {
			foreach ( (array) $taxonomy_terms as $term ) {
				if ( isset( $term['id'], $term['name'] ) ) {
					$terms[ (int) $term['id'] ] = $term;
				}
			}
		}

		return $terms;
	}
}
