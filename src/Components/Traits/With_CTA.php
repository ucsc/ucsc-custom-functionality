<?php declare(strict_types=1);

namespace UCSC\Blocks\Components\Traits;

trait With_CTA {

	public function get_cta(): string {
		if ( empty( $this->cta ) || empty( $this->cta['title'] ) || empty( $this->cta['url'] ) ) {
			return '';
		}

		return sprintf( '<a href="%s" target="%s">%s</a>', $this->cta['url'], $this->cta['target'] ?: '_self', $this->cta['title'] );
	}

}
