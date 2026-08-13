<?php
/**
 * Block template base class.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Template;

use WP_Block_Template;
use WP_Post;
use WP_Query;

/**
 * Base for the block templates the plugin injects into the Site Editor.
 *
 * These templates are not theme files. On first request the subclass writes
 * a wp_template post from an HTML file in src/views/templates/ and tags it
 * with a wp_theme term, after which WordPress treats it as a user-created
 * template that editors can modify.
 *
 * Because that term is the theme slug, the templates are bound to one theme;
 * renaming or switching themes orphans them silently. See #109.
 */
abstract class Template {

	/**
	 * Identifier for the template. Overridden per subclass.
	 *
	 * @var string
	 */
	public const NAME = '';
	/**
	 * Template slug, used as the wp_template post name.
	 *
	 * @var string
	 */
	public const SLUG = '';
	/**
	 * The wp_theme term templates are filed under.
	 *
	 * Hardcoded to the UCSC theme slug; see #109.
	 *
	 * @var string
	 */
	public const NAMESPACE = 'ucsc-2022';

	/**
	 * Template version. Overridden per subclass.
	 *
	 * @var string
	 */
	public const VERSION = '';
	/**
	 * Meta key reserved for storing a template's version.
	 *
	 * @var string
	 */
	public const TEMPLATE_VERSION = 'ucsc_template_version';

	/**
	 * Create the wp_template post backing this template.
	 *
	 * Called once, on first request.
	 *
	 * @return WP_Block_Template|null
	 */
	abstract protected function create_wp_block_template(): ?WP_Block_Template;

	/**
	 * Hook the template into the Site Editor's template lookup.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter(
			'get_block_templates',
			function ( $query_result, $query, $template_type ) {
				return $this->register( $query_result, $query, $template_type );
			},
			10,
			3
		);
	}

	/**
	 * The template's slug, used as the post name.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return static::SLUG;
	}

	/**
	 * The wp_theme term the template is filed under.
	 *
	 * @return string
	 */
	public function get_namespace(): string {
		return static::NAMESPACE;
	}

	/**
	 * Decide whether this template applies to the current request.
	 *
	 * Implemented per template.
	 *
	 * @param mixed  $query_result  Templates found so far.
	 * @param array  $query         The template query.
	 * @param string $template_type The template type being queried.
	 *
	 * @return mixed
	 */
	abstract public function register( $query_result, $query, $template_type );

	/**
	 * Find the stored template, creating it on first use.
	 *
	 * Returns null when the template could not be created.
	 *
	 * @return array|null
	 */
	public function register_template() {
		$wp_block_template = $this->find_block_template_by_post( $this->get_slug(), $this->get_namespace() );

		// If empty, this is our first time loading our Block Template. Let's create it.
		if ( ! $wp_block_template ) {
			$wp_block_template = $this->create_wp_block_template();
		}

		if ( ! $wp_block_template instanceof WP_Block_Template ) {
			return null;
		}

		return [ $wp_block_template ];
	}


	/**
	 * Build a WP_Block_Template from its stored post.
	 *
	 * Returns null when the post carries no wp_theme term, since without one
	 * the template cannot be addressed.
	 *
	 * @param WP_Post $post The stored wp_template post.
	 *
	 * @return WP_Block_Template|null
	 */
	public function hydrate_block_template_by_post( WP_Post $post ): ?WP_Block_Template {
		$terms = get_the_terms( $post, 'wp_theme' );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return null;
		}

		// Hydrate our template with the saved data.
		$template                 = new WP_Block_Template();
		$template->wp_id          = $post->ID;
		$template->id             = $terms[0]->name . '//' . $post->post_name;
		$template->theme          = $terms[0]->name;
		$template->content        = $post->post_content;
		$template->slug           = $post->post_name;
		$template->source         = 'custom';
		$template->type           = 'wp_template';
		$template->title          = $post->post_title;
		$template->description    = $post->post_excerpt;
		$template->status         = $post->post_status;
		$template->has_theme_file = false;
		$template->is_custom      = true;
		$template->author         = $post->post_author;
		$template->modified       = $post->post_modified;
		$template->post_types     = [ 'post' ];

		return $template;
	}

	/**
	 * Look up a stored template by slug and theme term.
	 *
	 * @param string $post_name The template slug.
	 * @param string $terms     The wp_theme term name.
	 *
	 * @return WP_Block_Template|null
	 */
	protected function find_block_template_by_post( string $post_name, string $terms = '' ): ?WP_Block_Template {
		$wp_query_args  = [
			'post_name__in'  => [ $post_name ],
			'post_type'      => 'wp_template',
			'post_status'    => [ 'auto-draft', 'draft', 'publish', 'trash' ],
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'tax_query'      => [
				[
					'taxonomy' => 'wp_theme',
					'field'    => 'name',
					'terms'    => $terms,
				],
			],
		];
		$template_query = new WP_Query( $wp_query_args );
		$posts          = $template_query->posts;
		if ( empty( $posts ) ) {
			return null;
		}

		$post = $posts[0] ?? null;

		if ( ! $post instanceof WP_Post ) {
			return null;
		}

		return $this->hydrate_block_template_by_post( $post );
	}
}
