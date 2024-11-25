<?php declare(strict_types=1);

namespace UCSC\Blocks\Components\Traits;

trait With_CTA {

	public function get_cta( array $classes = [] ): string {
		if ( empty( $this->cta ) || empty( $this->cta['title'] ) || empty( $this->cta['url'] ) ) {
			return '';
		}
		
		$classes = ! empty( $classes ) ? sprintf( 'class="%s"', implode( ' ', $classes ) ) : '';

		return sprintf( '<a href="%s"%s target="%s">%s</a>', $this->cta['url'], $classes, $this->cta['target'] ?: '_self', $this->cta['title'] );
	}

}
