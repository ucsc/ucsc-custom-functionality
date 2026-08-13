<?php
/**
 * Call-to-action rendering.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components\Traits;

/**
 * Renders the call-to-action link saved by an ACF link field.
 *
 * Expects the using class to expose a $cta property holding ACF's link array
 * of title, url and target.
 */
trait With_CTA {

	/**
	 * Render the call to action as an anchor.
	 *
	 * Returns an empty string when no link has been set, or when either the
	 * title or the URL is missing, so a partially filled field renders nothing
	 * rather than a broken link.
	 *
	 * The returned markup is fully escaped, so callers may echo it directly.
	 *
	 * @param array $classes Classes to apply to the anchor.
	 *
	 * @return string The anchor markup, or an empty string.
	 */
	public function get_cta( array $classes = [] ): string {
		if ( empty( $this->cta ) || empty( $this->cta['title'] ) || empty( $this->cta['url'] ) ) {
			return '';
		}

		$classes = ! empty( $classes ) ? sprintf( 'class="%s"', esc_attr( implode( ' ', $classes ) ) ) : '';

		return sprintf(
			'<a href="%s"%s target="%s">%s</a>',
			esc_url( $this->cta['url'] ),
			$classes,
			esc_attr( ! empty( $this->cta['target'] ) ? $this->cta['target'] : '_self' ),
			esc_html( $this->cta['title'] )
		);
	}
}
