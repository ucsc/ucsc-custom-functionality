<?php
/**
 * Custom post type base class.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Post_Types;

/**
 * Base for the plugin's custom post types.
 *
 * Subclasses supply the post type slug and its registration arguments.
 */
abstract class Post_Types {

	/**
	 * The post type slug. Overridden by each subclass.
	 *
	 * @var string
	 */
	public const NAME = '';

	/**
	 * Arguments passed to register_post_type().
	 *
	 * @return array
	 */
	abstract public function get_args(): array;

	/**
	 * Register the post type.
	 *
	 * Must run on init. Note that nothing flushes rewrite rules, so a new post
	 * type's permalinks 404 until they are re-saved; tracked in #108.
	 *
	 * @return void
	 */
	public function register(): void {
		register_post_type( static::NAME, $this->get_args() );
	}
}
