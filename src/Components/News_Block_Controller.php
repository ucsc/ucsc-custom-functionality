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
 * queried locally. Everything is cached in transients for 30 minutes.
 *
 * Featured images and terms (including Co-Authors Plus authors, which are
 * terms of the remote `author` taxonomy) are embedded in the posts response,
 * so a cold render is a single request. The one exception is a post with no
 * coauthors, which costs one cached request for the default author.
 */
class News_Block_Controller {

	/**
	 * Transient key prefix for cached items.
	 *
	 * Changed whenever the shape of the cached items changes, so items cached
	 * in an old shape are never read back as the new one.
	 *
	 * @var string
	 */
	public const POSTS = 'news_items';
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
	 * items are shared by every block with the same taxonomy selection.
	 *
	 * @var string
	 */
	private const EMBED = 'wp:featuredmedia,wp:term';
	/**
	 * Post fields requested from the API.
	 *
	 * The selected taxonomy's REST base is added per request. `_links` must
	 * stay, since the API drops the embeds without it. Like EMBED, this is
	 * not narrowed by the hide flags.
	 *
	 * @var string[]
	 */
	private const FIELDS = [ 'id', 'title', 'excerpt', 'link', 'date', 'featured_media', 'coauthors', 'tags', '_links', '_embedded' ];
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
	private const CACHE_EXPIRY = MINUTE_IN_SECONDS * 30;
	/**
	 * How long the last good items are kept for serving during an outage.
	 *
	 * @var int
	 */
	private const STALE_EXPIRY = DAY_IN_SECONDS;
	/**
	 * How long to wait after a failed fetch before trying again.
	 *
	 * @var int
	 */
	private const FAILURE_BACKOFF = MINUTE_IN_SECONDS * 5;
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
	 * Whether the news site could not be reached for this render.
	 *
	 * Set while a failure or its backoff is in effect, whether the block then
	 * shows the stale items or nothing. Read by the view to notify editors.
	 *
	 * @var bool
	 */
	private bool $fetch_failed = false;

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
	 * Whether a taxonomy and at least one term are selected.
	 *
	 * @return bool
	 */
	public function is_configured(): bool {
		return ! empty( $this->taxonomy ) && ! empty( $this->taxonomy_ids );
	}

	/**
	 * Whether the news site could not be reached for this render.
	 *
	 * Only meaningful after get_items() has run.
	 *
	 * @return bool
	 */
	public function has_fetch_failed(): bool {
		return $this->fetch_failed;
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
	 * The posts to render, with hidden fields blanked.
	 *
	 * Returns an empty array unless both a taxonomy and at least one term are
	 * selected, so an unconfigured block renders nothing rather than an
	 * arbitrary post list. Hidden fields are blanked here rather than in the
	 * view, so the view does no conditional work.
	 *
	 * @return array
	 */
	public function get_items(): array {
		if ( ! $this->is_configured() ) {
			return [];
		}

		$items = array_slice( $this->get_cached_items(), 0, $this->posts_per_page );

		return array_map( [ $this, 'apply_display_options' ], $items );
	}

	/**
	 * Every field of the selection's posts, shaped and cached.
	 *
	 * The cache holds the shaped items rather than the raw response, and is
	 * written only after a successful fetch, so a cache hit never extends its
	 * own expiry. The hide flags are not applied, so every block with the same
	 * taxonomy selection shares one entry.
	 *
	 * A failed fetch is not retried on every render: it serves the last good
	 * items (empty if there are none) and backs off for FAILURE_BACKOFF.
	 *
	 * @return array
	 */
	protected function get_cached_items(): array {
		$items = get_transient( $this->get_cache_key() );

		// An empty array is a cached "no posts" result, so test for an array rather than emptiness.
		if ( is_array( $items ) ) {
			return $items;
		}

		// A recent fetch failed; wait out the backoff rather than retrying on every render.
		if ( get_transient( $this->get_cache_key( 'failed' ) ) ) {
			$this->fetch_failed = true;

			return $this->get_stale_items();
		}

		$response = ( new News_Request() )->try_request(
			News_Request::POSTS_ENDPOINT,
			[
				'per_page'      => self::PER_PAGE,
				'_embed'        => self::EMBED,
				'_fields'       => implode( ',', array_unique( array_merge( self::FIELDS, [ $this->taxonomy ] ) ) ),
				$this->taxonomy => implode( ',', $this->taxonomy_ids ),
			]
		);

		if ( null === $response ) {
			$this->fetch_failed = true;
			set_transient( $this->get_cache_key( 'failed' ), true, self::FAILURE_BACKOFF );

			return $this->get_stale_items();
		}

		// No matching posts is a real result, so it is cached like any other.
		$items = array_map( [ $this, 'shape_item' ], $response );

		set_transient( $this->get_cache_key(), $items, self::CACHE_EXPIRY );
		set_transient( $this->get_cache_key( 'stale' ), $items, self::STALE_EXPIRY );

		return $items;
	}

	/**
	 * The last good items for this selection, kept for STALE_EXPIRY.
	 *
	 * Served while the news site is failing, so the block keeps showing recent
	 * posts instead of going empty.
	 *
	 * @return array
	 */
	protected function get_stale_items(): array {
		$items = get_transient( $this->get_cache_key( 'stale' ) );

		return is_array( $items ) ? $items : [];
	}

	/**
	 * Shape a post from the REST response into the fields the view reads.
	 *
	 * @param array $item A post from the REST response.
	 *
	 * @return array
	 */
	protected function shape_item( array $item ): array {
		return [
			'title'        => $item['title']['rendered'] ?? '',
			'excerpt'      => $item['excerpt']['rendered'] ?? '',
			'permalink'    => $item['link'] ?? '',
			'image'        => $this->get_item_attachment( $item ),
			'raw_date'     => $item['date'] ?? '',
			'publish_date' => isset( $item['date'] ) ? wp_date( get_option( 'date_format', 'F j, Y' ), strtotime( $item['date'] ) ) : '',
			'authors'      => $this->get_authors( $item ),
			'tags'         => $this->get_taxonomies( $item, true ),
			'categories'   => $this->get_taxonomies( $item ),
		];
	}

	/**
	 * Blank the fields this block hides.
	 *
	 * @param array $item A shaped item.
	 *
	 * @return array
	 */
	protected function apply_display_options( array $item ): array {
		if ( $this->hide_excerpt ) {
			$item['excerpt'] = '';
		}

		if ( $this->hide_image ) {
			$item['image'] = [];
		}

		if ( $this->hide_date ) {
			$item['raw_date']     = '';
			$item['publish_date'] = '';
		}

		if ( $this->hide_author ) {
			$item['authors'] = '';
		}

		if ( $this->hide_tags ) {
			$item['tags'] = [];
		}

		if ( $this->hide_category ) {
			$item['categories'] = [];
		}

		return $item;
	}

	/**
	 * Compose a transient key.
	 *
	 * The key embeds both the taxonomy and the selected term IDs, since term
	 * IDs alone are not unique across taxonomies.
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

		// Keyed by author ID alone, so every block configuration shares one entry.
		$cache_key = 'news_coauthor_' . self::DEFAULT_AUTHOR_ID;
		$user      = get_transient( $cache_key );

		if ( empty( $user ) ) {
			$user = ( new News_Request() )->request( News_Request::ENDPOINT_BASE . 'coauthors/' . self::DEFAULT_AUTHOR_ID );

			if ( empty( $user ) ) {
				return [];
			}

			set_transient( $cache_key, $user, self::CACHE_EXPIRY );
		}

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
