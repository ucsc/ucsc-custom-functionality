<?php
/**
 * Post header block field group.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

/**
 * Field group for the Post Header block.
 *
 * Chooses between the small and large featured-image treatments used at
 * the top of a single post.
 */
class Post_Header_Block extends ACF_Group {

	use With_CTA_Field;

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'ucsc_post_header_block';

	/**
	 * Layout selector field name.
	 *
	 * @var string
	 */
	public const LAYOUT = 'layout';
	/**
	 * Layout value: small image. The default.
	 *
	 * @var string
	 */
	public const LAYOUT_SMALL = 'layout_small';
	/**
	 * Layout value: large image.
	 *
	 * @var string
	 */
	public const LAYOUT_BIG = 'layout_big';

	/**
	 * Attach the group to the Post Header block.
	 *
	 * @return array
	 */
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

	/**
	 * Field group title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return esc_html__( 'Post Header', 'ucsc' );
	}

	/**
	 * Field group key.
	 *
	 * @return string
	 */
	protected function get_key(): string {
		return self::NAME;
	}

	/**
	 * The layout selector.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		return [
			$this->get_layout_field(),
		];
	}

	/**
	 * Featured image treatment, small by default.
	 *
	 * @return array
	 */
	protected function get_layout_field(): array {
		return [
			'type'          => 'radio',
			'name'          => self::LAYOUT,
			'key'           => $this->get_field_key( self::LAYOUT, self::NAME ),
			'label'         => esc_html__( 'Layout', 'ucsc' ),
			'choices'       => [
				self::LAYOUT_SMALL => esc_html__( 'Small image', 'ucsc' ),
				self::LAYOUT_BIG   => esc_html__( 'Big image', 'ucsc' ),
			],
			'default_value' => self::LAYOUT_SMALL,
		];
	}
}
