<?php
/**
 * Magazine block field group.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Traits\With_CTA_Field;

/**
 * Field group for the UCSC Magazine block.
 *
 * A two-line masthead above a repeater of magazine items, each rendered as
 * a tab with its own image, byline, description and call to action.
 */
class Magazine_Block extends ACF_Group {

	use With_CTA_Field;

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'ucsc_magazine_block';

	/**
	 * First masthead line, rendered light.
	 *
	 * @var string
	 */
	public const TITLE_LINE_1 = 'title_1';
	/**
	 * Second masthead line, rendered bold.
	 *
	 * @var string
	 */
	public const TITLE_LINE_2 = 'title_2';
	/**
	 * Subtitle field name.
	 *
	 * @var string
	 */
	public const SUBTITLE = 'subtitle';
	/**
	 * Repeater field name holding the magazine items.
	 *
	 * @var string
	 */
	public const ITEMS = 'items';
	/**
	 * Item title field name. Also the collapsed-row label.
	 *
	 * @var string
	 */
	public const ITEM_TITLE = 'item_title';
	/**
	 * Item byline field name.
	 *
	 * @var string
	 */
	public const ITEM_BYLINE = 'item_byline';
	/**
	 * Item image field name.
	 *
	 * @var string
	 */
	public const ITEM_IMAGE = 'item_image';
	/**
	 * Item description field name.
	 *
	 * @var string
	 */
	public const ITEM_DESC = 'item_description';

	/**
	 * Item call-to-action field name.
	 *
	 * @var string
	 */
	public const ITEM_CTA_FIELD = 'item_cta';

	/**
	 * Attach the group to the Magazine block.
	 *
	 * @return array
	 */
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

	/**
	 * Field group title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return esc_html__( 'UCSC Magazine', 'ucsc' );
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
	 * The masthead fields and the item repeater.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		return [
			$this->get_title_line_1_field(),
			$this->get_title_line_2_field(),
			$this->get_subtitle_field(),
			$this->get_items(),
		];
	}

	/**
	 * First masthead line.
	 *
	 * @return array
	 */
	protected function get_title_line_1_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::TITLE_LINE_1,
			'key'   => $this->get_field_key( self::TITLE_LINE_1, self::NAME ),
			'label' => esc_html__( 'Title Line 1 (Light)', 'ucsc' ),
		];
	}

	/**
	 * Second masthead line.
	 *
	 * @return array
	 */
	protected function get_title_line_2_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::TITLE_LINE_2,
			'key'   => $this->get_field_key( self::TITLE_LINE_2, self::NAME ),
			'label' => esc_html__( 'Title Line 2 (Bold)', 'ucsc' ),
		];
	}

	/**
	 * Subtitle shown beneath the masthead.
	 *
	 * @return array
	 */
	protected function get_subtitle_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::SUBTITLE,
			'key'   => $this->get_field_key( self::SUBTITLE, self::NAME ),
			'label' => esc_html__( 'Subtitle', 'ucsc' ),
		];
	}

	/**
	 * The repeater of magazine items.
	 *
	 * Rows collapse to their title in the editor.
	 *
	 * @return array
	 */
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

	/**
	 * An item's title.
	 *
	 * @return array
	 */
	protected function get_item_title(): array {
		return [
			'type'  => 'text',
			'name'  => self::ITEM_TITLE,
			'key'   => $this->get_field_key( self::ITEM_TITLE, self::ITEMS ),
			'label' => esc_html__( 'Title', 'ucsc' ),
		];
	}

	/**
	 * An item's byline.
	 *
	 * @return array
	 */
	protected function get_item_byline(): array {
		return [
			'type'  => 'text',
			'name'  => self::ITEM_BYLINE,
			'key'   => $this->get_field_key( self::ITEM_BYLINE, self::ITEMS ),
			'label' => esc_html__( 'Byline', 'ucsc' ),
		];
	}

	/**
	 * An item's image. Returned as an attachment ID.
	 *
	 * @return array
	 */
	protected function get_image_field(): array {
		return [
			'label'         => esc_html__( 'Image', 'ucsc' ),
			'key'           => $this->get_field_key( self::ITEM_IMAGE, self::ITEMS ),
			'name'          => self::ITEM_IMAGE,
			'type'          => 'image',
			'return_format' => 'id',
		];
	}

	/**
	 * An item's description.
	 *
	 * @return array
	 */
	protected function get_item_desc(): array {
		return [
			'type'  => 'textarea',
			'name'  => self::ITEM_DESC,
			'key'   => $this->get_field_key( self::ITEM_DESC, self::ITEMS ),
			'label' => esc_html__( 'Description', 'ucsc' ),
		];
	}
}
