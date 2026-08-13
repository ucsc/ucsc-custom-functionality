<?php
/**
 * Photo of the Week meta fields.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Object_Meta;

use UCSC\Blocks\Blocks\ACF_Group;
use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;

/**
 * ACF field group attached to the Photo of the Week post type.
 *
 * Supplies the photograph itself and its credit; the post title carries the
 * caption.
 */
class Photo_Of_The_Week_Meta extends ACF_Group {

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'photo_of_the_week_post';

	/**
	 * Photographer credit field name.
	 *
	 * @var string
	 */
	public const PHOTOGRAPHER = 'photographer';

	/**
	 * Image field name.
	 *
	 * @var string
	 */
	public const IMAGE = 'image';

	/**
	 * Attach the group to the Photo of the Week post type.
	 *
	 * @return array
	 */
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

	/**
	 * Field group title, shown in the editor.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return esc_html__( 'Photo of the Week', 'ucsc' );
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
	 * The fields in this group.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		return [
			$this->get_photographer(),
			$this->get_image(),
		];
	}

	/**
	 * Photographer credit. Plain text.
	 *
	 * @return array
	 */
	protected function get_photographer(): array {
		return [
			'label' => esc_html__( 'Photographer', 'ucsc' ),
			'name'  => self::PHOTOGRAPHER,
			'key'   => self::PHOTOGRAPHER,
			'type'  => 'text',
		];
	}

	/**
	 * The photograph. Required; returned as an array.
	 *
	 * @return array
	 */
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
