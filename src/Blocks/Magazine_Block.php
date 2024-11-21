<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

class Magazine_Block extends ACF_Group {
	
	use With_CTA_Field;

	public const NAME = 'ucsc_magazine';
	
	public const TITLE       = 'title';
	public const OVERLINE    = 'overline';
	public const ITEMS       = 'items';
	public const ITEM_TITLE  = 'item_title';
	public const ITEM_BYLINE = 'item_byline';
	public const ITEM_IMAGE  = 'item_image';
	public const ITEM_DESC   = 'item_description';

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
			$this->get_title_field(),
			$this->get_overline_field(),
			$this->get_items(),
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

	protected function get_overline_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::OVERLINE,
			'key'   => $this->get_field_key( self::OVERLINE, self::NAME ),
			'label' => esc_html__( 'Overline', 'ucsc' ),
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
    
    public function get_image_field(): array {
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
