<?php
/**
 * Third-party plugin integrations.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Integrations;

/**
 * Registers integrations with Yoast SEO and ACF.
 */
class Integrations_Subscriber {

	/**
	 * Taxonomies intended to support a Yoast primary term.
	 *
	 * Note: only 'academics' is actually registered — the loop in init()
	 * ignores its own iteration variable. Tracked in #105; left as-is here so
	 * this stays a documentation-only change.
	 *
	 * @var string[]
	 */
	public const PRIMARY_TAX_SUPPORT = [
		'academics',
		'administration',
		'person',
		'section',
		'kind',
	];

	/**
	 * Register the Yoast primary-term and ACF toolbar filters.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter(
			'wpseo_primary_term_taxonomies',
			static function ( $taxonomies, $post_type, $all_taxonomies ) {
				foreach ( self::PRIMARY_TAX_SUPPORT as $tax ) {
					if ( ! isset( $all_taxonomies['academics'] ) ) {
						continue;
					}

					$taxonomies['academics'] = $all_taxonomies['academics'];
				}

				return $taxonomies;
			},
			10,
			3
		);

		add_filter(
			'acf/fields/wysiwyg/toolbars',
			static function ( $toolbars ) {
				return ( new ACF_Toolbars() )->register_simple_toolbar( (array) $toolbars );
			},
			10,
			1
		);
	}
}
