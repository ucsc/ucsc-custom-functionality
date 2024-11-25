<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Contracts\CTA_Field;
use UCSC\Blocks\Blocks\Traits\With_CTA_Field;
use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;

class Photo_Of_The_Week_Block extends ACF_Group implements CTA_Field {
	
	use With_CTA_Field;

	public const NAME = 'ucsc_photo_of_the_week';
	
	public const TITLE = 'title';
	public const PHOTO = 'ucsc_photo_single';

	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/photo-of-the-week-block',
				],
			],
		];
	}

	protected function get_title(): string {
		return esc_html__( 'UCSC Photo Of The Week Block', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		return [
			$this->get_title_field(),
			$this->get_cta_field( self::NAME, esc_html__( 'Button URL', 'ucsc' ) ),
			$this->get_photo_field(),
		];
	}

	protected function get_title_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::TITLE,
			'key'   => $this->get_field_key( self::TITLE, self::NAME ),
			'label' => esc_html__( 'Title', 'ucsc' ),
		];
	}
	
	protected function get_photo_field(): array {
		return [
			'type'          => 'post_object',
			'name'          => self::PHOTO,
			'key'           => $this->get_field_key( self::PHOTO, self::NAME ),
			'label'         => esc_html__( 'Photo Of The Week', 'ucsc' ),
			'post_type'     => [
				Photo_Of_The_Week::NAME,
			],
			'return_format' => 'id',
			'ui'            => 1,
			'post_status'   => [
				'publish',
			],
		];
	}

}
