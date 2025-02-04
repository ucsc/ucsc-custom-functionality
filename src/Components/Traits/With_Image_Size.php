<?php declare(strict_types=1);

namespace UCSC\Blocks\Components\Traits;

trait With_Image_Size {

	public function build_srcset( array $image = [] ): string {
		if ( empty( $image ) ) {
			return '';
		}

		$urls  = [];
		$sizes = $image['sizes'] ?? [];
		
		if ( empty( $sizes ) ) {
			return '';
		}
		
		foreach ( $sizes as $size ) {
			$urls[] = $image['url'] . ' ' . $size['width'] . 'w ' . $size['height'] . 'h';
		}

		return implode( ', ', $urls );
	}
	
}
