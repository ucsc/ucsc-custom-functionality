<?php declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Photo_Of_The_Week_Block;
use UCSC\Blocks\Components\Traits\With_CTA;
use UCSC\Blocks\Components\Traits\With_Image_Size;
use UCSC\Blocks\Object_Meta\Photo_Of_The_Week_Meta;

class Photo_Of_The_Week_Block_Controller {

	use With_Image_Size;
	use With_CTA;

	protected array $block;
	protected array $cta;

	public function __construct( $block ) {
		$this->block = (array) $block;
		$this->cta   = (array) get_field( Photo_Of_The_Week_Block::CTA );
	}

	public function get_attributes(): string {
		return wp_kses_data( get_block_wrapper_attributes([
			'class' => implode(' ', [
				'ucsc-photo-of-the-week-block',
				'alignfull',
				'has-black-background-color',
				'has-white-color',
				'has-global-padding',
				'is-layout-constrained',
			]),
		]) );
	}
	
	public function get_title(): string {
		$title = (string) get_field( Photo_Of_The_Week_Block::TITLE );
		
		return strlen( $title ) > 0 ? $title : '';
	}
	
	public function get_photo(): ?array {
		$photo = (string) get_field( Photo_Of_The_Week_Block::PHOTO );
		
		if ( empty( $photo ) ) {
			return null;
		}
		
		$image_data = $this->get_photo_image( $photo );
		
		if ( ! empty( $image_data ) ) {
			$image = sprintf(
				'<img src="%s" srcset="%s" alt="%s" class="photo-of-the-week__image" />',
				$image_data['url'],
				$this->build_srcset( $image_data ),
				get_the_title( get_the_ID() )
			);
		}
		
		return [
			'id'       => $photo,
			'image'    => $image ?: '',
			'download' => $image_data['url'] ?: '',
			'title'    => get_the_title( $photo ),
			'author'   => $this->get_photo_author( $photo ),
		];
	}
	
	public function get_photo_author( $photo_id ): string {
		return (string) get_field( Photo_Of_The_Week_Meta::PHOTOGRAPHER, $photo_id );
	}
	
	public function get_photo_image( $photo_id ): array {
		$image = get_field( Photo_Of_The_Week_Meta::IMAGE, $photo_id );

		if ( empty( $image ) || $image['ID'] < 1 ) {
			return [];
		}

		$image_meta = wp_get_attachment_metadata( $image['ID'] );

		return array_merge( [
			'id'  => $image['ID'],
			'url' => $image['url'],
		], $image_meta );
	}

}
