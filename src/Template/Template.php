<?php declare(strict_types=1);

namespace UCSC\Blocks\Template;

use WP_Block_Template;
use WP_Post;
use WP_Query;

abstract class Template {
	
	public const NAME      = '';
	public const SLUG      = '';
	public const NAMESPACE = 'ucsc-2022';
	
	public const VERSION          = '';
	public const TEMPLATE_VERSION = 'ucsc_template_version';
	
	abstract protected function create_wp_block_template(): ?WP_Block_Template;
	
	public function init(): void {
		add_filter( 'get_block_templates', function ( $query_result, $query, $template_type ) {
			return $this->register( $query_result, $query, $template_type );
		}, 10, 3 );
	}

	public function get_slug(): string {
		return static::SLUG;
	}

	public function get_namespace(): string {
		return static::NAMESPACE;
	}
    
    abstract public function register( $query_result, $query, $template_type );

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
