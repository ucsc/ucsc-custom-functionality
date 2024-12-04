<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Press_Inquiries_Block;

class Press_Inquiries_Controller {

	protected array $block;
	protected string $panel_id;

	public function __construct( $block ) {
		$this->block    = (array) $block;
		$this->panel_id = wp_unique_id( 'press-inquiries-block-' );
	}

	public function get_attributes(): string {
		return wp_kses_data( get_block_wrapper_attributes([
			'class' => implode( ' ', [
				'ucsc-press-inquiries-block',
				'alignfull',
				'is-layout-constrained',
				'has-global-padding',
			] ),
		]) );
	}
	
	public function get_press_contacts(): array {
		$contacts = get_field( Press_Inquiries_Block::PRESS_INQUIRIES );
		
		if ( empty( $contacts ) ) {
			return [];
		}
		
		return $contacts;
	}

	public function get_panel_id(): string {
		return esc_attr( $this->panel_id );
	}
	
	public function get_media_text(): string {
		return (string) get_field( Press_Inquiries_Block::MEDIA_TEXT );
	}

	public function get_media_file(): string {
		return (string) get_field( Press_Inquiries_Block::MEDIA_FILE );
	}

	public function get_media_image(): string {
		return (string) get_field( Press_Inquiries_Block::MEDIA_IMAGE );
	}
	
}
