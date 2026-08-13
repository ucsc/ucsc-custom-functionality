<?php
/**
 * Press inquiries block field group.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Integrations\ACF_Toolbars;

/**
 * Field group for the Press Inquiries block.
 *
 * Up to two press contacts, alongside optional downloadable assets and a
 * short brief.
 */
class Press_Inquiries_Block extends ACF_Group {

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'ucsc_press_inquiries';

	/**
	 * Overline field name. Currently unused by the field set.
	 *
	 * @var string
	 */
	public const POST_OVERLINE = 'ucsc_post_overline';
	/**
	 * Repeater field name holding the press contacts.
	 *
	 * @var string
	 */
	public const PRESS_INQUIRIES = 'ucsc_press_inquiries';
	/**
	 * Contact name field name. Also the collapsed-row label.
	 *
	 * @var string
	 */
	public const PRESS_NAME = 'ucsc_press_name';
	/**
	 * Contact email field name.
	 *
	 * @var string
	 */
	public const PRESS_EMAIL = 'ucsc_press_email';
	/**
	 * Contact phone field name.
	 *
	 * @var string
	 */
	public const PRESS_PHONE = 'ucsc_press_phone';

	/**
	 * Downloadable paper field name.
	 *
	 * @var string
	 */
	public const MEDIA_FILE = 'ucsc_media_file';
	/**
	 * Downloadable image field name.
	 *
	 * @var string
	 */
	public const MEDIA_IMAGE = 'ucsc_media_image';
	/**
	 * Brief field name.
	 *
	 * @var string
	 */
	public const MEDIA_TEXT = 'ucsc_media_text';

	/**
	 * Attach the group to the Press Inquiries block.
	 *
	 * @return array
	 */
	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'ucsc-custom-functionality/press-inquiries',
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
		return esc_html__( 'Press Inquiries', 'ucsc' );
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
	 * The contacts repeater, downloadable assets and brief.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		return [
			$this->get_post_inquiries(),
			$this->get_media(),
			$this->get_image(),
			$this->get_text_field(),
		];
	}

	/**
	 * The repeater of press contacts, capped at two.
	 *
	 * Rows collapse to the contact name in the editor.
	 *
	 * @return array
	 */
	protected function get_post_inquiries(): array {
		return [
			'key'          => $this->get_field_key( self::PRESS_INQUIRIES, self::NAME ),
			'type'         => 'repeater',
			'name'         => self::PRESS_INQUIRIES,
			'collapsed'    => $this->get_field_key( self::PRESS_NAME, self::PRESS_INQUIRIES ),
			'sub_fields'   => [
				$this->get_name_field(),
				$this->get_email_field(),
				$this->get_phone_field(),
			],
			'button_label' => esc_html__( 'Add Inquiry', 'ucsc' ),
			'layout'       => 'block',
			'max'          => 2,
		];
	}

	/**
	 * A contact's name.
	 *
	 * @return array
	 */
	protected function get_name_field(): array {
		return [
			'type'  => 'text',
			'key'   => $this->get_field_key( self::PRESS_NAME, self::PRESS_INQUIRIES ),
			'name'  => self::PRESS_NAME,
			'label' => esc_html__( 'Inquiry Name', 'ucsc' ),
		];
	}

	/**
	 * A contact's email address. Stored as plain text.
	 *
	 * @return array
	 */
	protected function get_email_field(): array {
		return [
			'type'  => 'text',
			'key'   => $this->get_field_key( self::PRESS_EMAIL, self::PRESS_INQUIRIES ),
			'name'  => self::PRESS_EMAIL,
			'label' => esc_html__( 'Inquiry Email', 'ucsc' ),
		];
	}

	/**
	 * A contact's phone number. Stored as plain text.
	 *
	 * @return array
	 */
	protected function get_phone_field(): array {
		return [
			'type'  => 'text',
			'key'   => $this->get_field_key( self::PRESS_PHONE, self::PRESS_INQUIRIES ),
			'name'  => self::PRESS_PHONE,
			'label' => esc_html__( 'Inquiry Phone', 'ucsc' ),
		];
	}

	/**
	 * An optional downloadable paper. Returned as a URL.
	 *
	 * @return array
	 */
	protected function get_media(): array {
		return [
			'label'         => esc_html__( 'Access paper', 'ucsc' ),
			'name'          => self::MEDIA_FILE,
			'key'           => $this->get_field_key( self::MEDIA_FILE, self::NAME ),
			'type'          => 'file',
			'required'      => 0,
			'return_format' => 'url',
		];
	}

	/**
	 * An optional downloadable image. Returned as a URL.
	 *
	 * @return array
	 */
	protected function get_image(): array {
		return [
			'label'         => esc_html__( 'Image Download', 'ucsc' ),
			'name'          => self::MEDIA_IMAGE,
			'key'           => $this->get_field_key( self::MEDIA_IMAGE, self::NAME ),
			'type'          => 'image',
			'required'      => 0,
			'return_format' => 'url',
		];
	}

	/**
	 * A short brief.
	 *
	 * Uses the cut-down toolbar so editors cannot introduce formatting the
	 * block layout does not support.
	 *
	 * @return array
	 */
	protected function get_text_field(): array {
		return [
			'type'         => 'wysiwyg',
			'key'          => $this->get_field_key( self::MEDIA_TEXT, self::PRESS_INQUIRIES ),
			'name'         => self::MEDIA_TEXT,
			'label'        => esc_html__( 'Brief', 'ucsc' ),
			'tabs'         => 'visual',
			'media_upload' => 0,
			'toolbar'      => ACF_Toolbars::SIMPLE,
		];
	}
}
