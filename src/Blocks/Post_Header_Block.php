<?php declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

class Post_Header_Block extends ACF_Group {
	
	use With_CTA_Field;

	public const NAME = 'ucsc_post_header_block';
	
	public const LAYOUT       = 'layout';
	public const LAYOUT_SMALL = 'layout_small';
	public const LAYOUT_BIG   = 'layout_big';
	
	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/post-header-block',
				],
			],
		];
	}

	protected function get_title(): string {
		return esc_html__( 'Post Header', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		return [
			$this->get_layout_field(),
		];
	}
	
	protected function get_layout_field(): array {
		return [
			'type'          => 'radio',
			'name'          => self::LAYOUT,
			'key'           => $this->get_field_key( self::LAYOUT, self::NAME ),
			'label'         => esc_html__( 'Layout', 'ucsc' ),
			'choices'       => [
				self::LAYOUT_BIG   => esc_html__( 'Big image', 'ucsc' ),
				self::LAYOUT_SMALL => esc_html__( 'Small image', 'ucsc' ),
			],
			'default_value' => self::LAYOUT_BIG,
		];
	}

}
