<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

class Magazine_Block extends ACF_Group {
	
	use With_CTA_Field;

	public const NAME = 'ucsc_magazine_block';
	
	public const TITLE_LINE_1 = 'title_1';
	public const TITLE_LINE_2 = 'title_2';
	public const SUBTITLE     = 'subtitle';
	public const ITEMS        = 'items';
	public const ITEM_TITLE   = 'item_title';
	public const ITEM_BYLINE  = 'item_byline';
	public const ITEM_IMAGE   = 'item_image';
	public const ITEM_DESC    = 'item_description';

	public const ITEM_CTA_FIELD = 'item_cta';

	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/magazine-block',
				],
			],
		];
	}

	protected function get_title(): string {
		return esc_html__( 'UCSC Magazine', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		return [
			$this->get_title_line_1_field(),
			$this->get_title_line_2_field(),
			$this->get_subtitle_field(),
			$this->get_items(),
		];
	}

	protected function get_title_line_1_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::TITLE_LINE_1,
			'key'   => $this->get_field_key( self::TITLE_LINE_1, self::NAME ),
			'label' => esc_html__( 'Title Line 1 (Light)', 'ucsc' ),
		];
	}

	protected function get_title_line_2_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::TITLE_LINE_2,
			'key'   => $this->get_field_key( self::TITLE_LINE_2, self::NAME ),
			'label' => esc_html__( 'Title Line 2 (Bold)', 'ucsc' ),
		];
	}

	protected function get_subtitle_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::SUBTITLE,
			'key'   => $this->get_field_key( self::SUBTITLE, self::NAME ),
			'label' => esc_html__( 'Subtitle', 'ucsc' ),
		];
	}

	protected function get_items(): array {
		return [
			'key'          => $this->get_field_key( self::ITEMS, self::NAME ),
			'type'         => 'repeater',
			'name'         => self::ITEMS,
			'collapsed'    => $this->get_field_key( self::ITEM_TITLE, self::ITEMS ),
			'sub_fields'   => [
				$this->get_item_title(),
				$this->get_item_byline(),
				$this->get_image_field(),
				$this->get_item_desc(),
				$this->get_cta_field( self::ITEMS, 'CTA', self::ITEM_CTA_FIELD ),
			],
			'button_label' => esc_html__( 'Add Magazine', 'ucsc' ),
			'layout'       => 'block',
		];
	}
	
	protected function get_item_title(): array {
		return [
			'type'  => 'text',
			'name'  => self::ITEM_TITLE,
			'key'   => $this->get_field_key( self::ITEM_TITLE, self::ITEMS ),
			'label' => esc_html__( 'Title', 'ucsc' ),
		];
	}

	protected function get_item_byline(): array {
		return [
			'type'  => 'text',
			'name'  => self::ITEM_BYLINE,
			'key'   => $this->get_field_key( self::ITEM_BYLINE, self::ITEMS ),
			'label' => esc_html__( 'Byline', 'ucsc' ),
		];
	}

	protected function get_image_field(): array {
		return [
			'label'         => esc_html__( 'Image', 'ucsc' ),
			'key'           => $this->get_field_key( self::ITEM_IMAGE, self::ITEMS ),
			'name'          => self::ITEM_IMAGE,
			'type'          => 'image',
			'return_format' => 'id',
		];
	}

	protected function get_item_desc(): array {
		return [
			'type'  => 'textarea',
			'name'  => self::ITEM_DESC,
			'key'   => $this->get_field_key( self::ITEM_DESC, self::ITEMS ),
			'label' => esc_html__( 'Description', 'ucsc' ),
		];
	}

}
