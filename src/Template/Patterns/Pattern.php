<?php
/**
 * Block pattern base class.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Template\Patterns;

/**
 * Base for block patterns registered by the plugin.
 *
 * Currently has no concrete subclasses; kept as the extension point for
 * patterns registered alongside the custom templates.
 */
abstract class Pattern {

	/**
	 * Pattern slug. Overridden per subclass.
	 *
	 * @var string
	 */
	public const SLUG = '';
	/**
	 * Pattern namespace.
	 *
	 * @var string
	 */
	public const NAMESPACE = 'ucsc-custom-functionality';

	/**
	 * Arguments passed to register_block_pattern().
	 *
	 * @return array
	 */
	abstract public function get_args(): array;

	/**
	 * Register the pattern.
	 *
	 * @return void
	 */
	abstract public function register(): void;

	/**
	 * Compose the namespaced pattern name.
	 *
	 * @return string
	 */
	protected function build_pattern_name(): string {
		return sprintf( '%s/%s', self::NAMESPACE, static::SLUG );
	}
}
