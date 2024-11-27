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
				'has-lightest-gray-background-color',
				'has-global-padding',
				'is-layout-constrained',
			]),
		]) );
	}

	public function get_title_line_1(): string {
		$overline = (string) get_field( Magazine_Block::TITLE_LINE_1 );

		return strlen( $overline ) < 1 ? '' : $overline;
	}
	
	public function get_title_line_2(): string {
		$title = (string) get_field( Magazine_Block::TITLE_LINE_2 );
		
		return strlen( $title ) < 1 ? '' : $title;
	}
	
	public function get_subtitle(): string {
		$subtitle = (string) get_field( Magazine_Block::SUBTITLE );
		
		return strlen( $subtitle ) < 1 ? '' : $subtitle;
	}

	public function get_magazines(): array {
		$magazines = (array) get_field( Magazine_Block::ITEMS );
		
		if ( empty( $magazines ) ) {
			return [];
		}
		
		return array_filter($magazines, static function ($magazine) {
			return is_array( $magazine ) && ! empty( $magazine );
		});
	}
	
	public function make_tab_key( array $magazine, int $index, string $element = '' ): string {
		return sprintf( 
			'%s-%s%s',
			sanitize_title( $magazine[ Magazine_Block::ITEM_TITLE ] ),
			$index + 1,
			$element ? '__' . esc_html( $element ) : ''
		);
	}
	
	public function get_cta( array $magazine ): array {
		if ( empty( $magazine[ Magazine_Block::ITEM_CTA_FIELD ] ) || empty( $magazine[ Magazine_Block::ITEM_CTA_FIELD ]['title'] ) || empty( $magazine[ Magazine_Block::ITEM_CTA_FIELD ]['url'] ) ) {
			return [];
		}

		return [
			'title'  => esc_html( $magazine[ Magazine_Block::ITEM_CTA_FIELD ]['title'] ),
			'url'    => esc_url( $magazine[ Magazine_Block::ITEM_CTA_FIELD ]['url'] ),
			'target' => esc_attr( $magazine[ Magazine_Block::ITEM_CTA_FIELD ]['target'] ?: '_self' ),
		];
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
