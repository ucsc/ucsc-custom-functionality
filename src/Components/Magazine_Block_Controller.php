<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Magazine_Block;
use UCSC\Blocks\Components\Traits\With_Image_Size;

class Magazine_Block_Controller {

	use With_Image_Size;

	protected array $block;

	public function __construct( $block ) {
		$this->block = (array) $block;
	}

	public function get_attributes(): string {
		return wp_kses_data( get_block_wrapper_attributes([
			'class' => implode(' ', [
				'ucsc-magazine-block',
				'alignfull',
				'has-light-gray-background-color',
				'has-global-padding',
				'is-layout-constrained',
			]),
		]) );
	}
	
	public function get_title(): string {
		$title = (string) get_field( Magazine_Block::TITLE );
		
		return strlen( $title ) < 1 ? '' : $title;
	}

	public function get_overline(): string {
		$overline = (string) get_field( Magazine_Block::OVERLINE );

		return strlen( $overline ) < 1 ? '' : $overline;
	}
	
	public function get_magazines(): array {
		$magazines = (array) get_field( Magazine_Block::ITEMS );
		
		if ( empty( $magazines ) ) {
			return [];
		}
		
		return $magazines;
	}
	
	public function make_tab_key( string $item, int $index ): string {
		return sprintf( '%s-%s', sanitize_title( $item ), $index + 1 );
	}
	
	public function get_cta( array $magazine ): string {
		if ( empty( $magazine[ Magazine_Block::ITEM_CTA_FIELD ] ) || empty( $magazine[ Magazine_Block::ITEM_CTA_FIELD ]['title'] ) || empty( $magazine[ Magazine_Block::ITEM_CTA_FIELD ]['url'] ) ) {
			return '';
		}

		return sprintf( 
			'<a href="%s" target="%s">%s</a>', 
			$magazine[ Magazine_Block::ITEM_CTA_FIELD ]['url'], 
			$magazine[ Magazine_Block::ITEM_CTA_FIELD ]['target'] ?: '_self', 
			$magazine[ Magazine_Block::ITEM_CTA_FIELD ]['title'] 
		);
	}
	
	public function get_image( array $magazine ): string {
		if ( empty( $magazine[ Magazine_Block::ITEM_IMAGE ] ) ) {
			return '';
		}

		$image_id   = $magazine[ Magazine_Block::ITEM_IMAGE ];
		$image_meta = $image_id > 0 ? wp_get_attachment_metadata( $image_id ) : [];
		$image_url  = wp_get_attachment_url( $image_id );
		
		return sprintf(
			'<img 
				src="%s" 
				srcset="%s" 
				class="" 
			/>',
			$image_url,
			$this->build_srcset( array_merge( [ 'id' => $image_id, 'url' => $image_url ], $image_meta ) )
		);
	}
	
	public function get_description( array $magazine ): string {
		$description = (string) $magazine[ Magazine_Block::ITEM_DESC ];

		return strlen( $description ) < 1 ? '' : $description;
	}
	
}
