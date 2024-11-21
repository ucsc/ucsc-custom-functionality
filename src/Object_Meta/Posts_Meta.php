<?php declare(strict_types=1);

namespace UCSC\Blocks\Object_Meta;

use UCSC\Blocks\Blocks\ACF_Group;
use UCSC\Blocks\Integrations\ACF_Toolbars;

class Posts_Meta extends ACF_Group {

	public const NAME            = 'ucsc_post_setting';
	public const POST_OVERLINE   = 'ucsc_post_overline';
	public const PRESS_INQUIRIES = 'ucsc_press_inquiries';
	public const PRESS_NAME      = 'ucsc_press_name';
	public const PRESS_EMAIL     = 'ucsc_press_email';
	public const PRESS_PHONE     = 'ucsc_press_phone';
	
	public const MEDIA_FILE  = 'ucsc_media_file';
	public const MEDIA_IMAGE = 'ucsc_media_image';
	public const MEDIA_TEXT  = 'ucsc_media_text';
	
	protected function get_locations(): array {
		return [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				],
			],
		];
	}

	protected function get_title(): string {
		return esc_html__( 'Post Meta', 'ucsc' );
	}

	protected function get_key(): string {
		return self::NAME;
	}

	protected function get_fields(): array {
		return [
			$this->get_post_overline(),
			$this->get_post_inquiries(),
            $this->get_media(),
			$this->get_image(),
			$this->get_text_field(),
		];
	}
	
	protected function get_post_overline(): array {
		return [
			'type'  => 'text',
			'key'   => self::POST_OVERLINE,
			'name'  => self::POST_OVERLINE,
			'label' => esc_html__( 'Post Overline', 'ucsc' ),
		];
	}
	
	protected function get_post_inquiries(): array {
		return [
			'key'          => self::PRESS_INQUIRIES,
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
	
	protected function get_name_field(): array {
		return [
			'type'  => 'text',
			'key'   => self::PRESS_NAME,
			'name'  => self::PRESS_NAME,
			'label' => esc_html__( 'Inquiry Name', 'ucsc' ),
		];
	}

	protected function get_email_field(): array {
		return [
			'type'  => 'text',
			'key'   => self::PRESS_EMAIL,
			'name'  => self::PRESS_EMAIL,
			'label' => esc_html__( 'Inquiry Email', 'ucsc' ),
		];
	}

	protected function get_phone_field(): array {
		return [
			'type'  => 'text',
			'key'   => self::PRESS_PHONE,
			'name'  => self::PRESS_PHONE,
			'label' => esc_html__( 'Inquiry Phone', 'ucsc' ),
		];
	}

	protected function get_media(): array {
		return [
			'label'         => esc_html__( 'Access paper', 'ucsc' ),
			'name'          => self::MEDIA_FILE,
			'key'           => self::MEDIA_FILE,
			'type'          => 'file',
			'required'      => 0,
			'return_format' => 'array',
		];
	}

	protected function get_image(): array {
		return [
			'label'         => esc_html__( 'Image Download', 'ucsc' ),
			'name'          => self::MEDIA_IMAGE,
			'key'           => self::MEDIA_IMAGE,
			'type'          => 'image',
			'required'      => 0,
			'return_format' => 'array',
		];
	}

	protected function get_text_field(): array {
		return [
			'type'         => 'wysiwyg',
			'key'          => self::MEDIA_TEXT,
			'name'         => self::MEDIA_TEXT,
			'label'        => esc_html__( 'Brief', 'ucsc' ),
			'tabs'         => 'visual',
			'media_upload' => 0,
			'toolbar'      => ACF_Toolbars::SIMPLE,
		];
	}
	
}
