<?php declare(strict_types=1);

namespace UCSC\Blocks\Object_Meta;

use UCSC\Blocks\Blocks\ACF_Group;
use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;

class Photo_Of_The_Week_Meta extends ACF_Group {
	
	public const NAME = 'photo_of_the_week_post';
	
	public const PHOTOGRAPHER = 'photographer';
	public const IMAGE        = 'image';

	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => Photo_Of_The_Week::NAME,
				],
			],
		];
	}

	protected function get_title(): string {
		return esc_html__( 'Photo of the Week', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		return [
			$this->get_photographer(),
			$this->get_image(),
		];
	}
	
	protected function get_photographer(): array {
		return [
			'label' => esc_html__( 'Photographer', 'ucsc' ),
			'name'  => self::PHOTOGRAPHER,
			'key'   => self::PHOTOGRAPHER,
			'type'  => 'text',
		];
	}

	protected function get_image(): array {
		return [
			'label'         => esc_html__( 'Image', 'ucsc' ),
			'name'          => self::IMAGE,
			'key'           => self::IMAGE,
			'type'          => 'image',
			'required'      => 1,
			'return_format' => 'array',
		];
	}

}
