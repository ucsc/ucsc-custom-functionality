<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Object_Meta\Posts_Meta;

class Press_Inquiries_Controller {

	protected array $block;

	public function __construct( $block ) {
		$this->block = (array) $block;
	}
	
	public function get_press_contacts(): array {
		$contacts = get_field( Posts_Meta::PRESS_INQUIRIES, get_the_ID() );
        
        if ( empty( $contacts ) ) {
            return [];
        }
        
        return $contacts;
	}
	
	public function get_media_text(): string {
		return (string) get_field( Posts_Meta::MEDIA_TEXT, get_the_ID() );
	}

	public function get_media_file(): string {
		return (string) get_field( Posts_Meta::MEDIA_FILE, get_the_ID() );
	}

	public function get_media_image(): string {
		return (string) get_field( Posts_Meta::MEDIA_IMAGE, get_the_ID() );
	}
	
}
