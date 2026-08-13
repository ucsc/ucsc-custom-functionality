<?php
/**
 * Photo of the Week block field group.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Blocks;

use UCSC\Blocks\Blocks\Contracts\CTA_Field;
use UCSC\Blocks\Blocks\Traits\With_CTA_Field;
use UCSC\Blocks\Post_Types\Photo_Of_The_Week\Photo_Of_The_Week;

/**
 * Field group for the Photo of the Week block.
 *
 * Selects a single published Photo of the Week entry to feature, with a
 * heading and a link to the full archive.
 */
class Photo_Of_The_Week_Block extends ACF_Group implements CTA_Field {

	use With_CTA_Field;

	/**
	 * Field group name and key.
	 *
	 * @var string
	 */
	public const NAME = 'ucsc_photo_of_the_week';

	/**
	 * Heading field name.
	 *
	 * @var string
	 */
	public const TITLE = 'title';
	/**
	 * Photo selector field name.
	 *
	 * @var string
	 */
	public const PHOTO = 'ucsc_photo_single';

	/**
	 * Attach the group to the Photo of the Week block.
	 *
	 * @return array
	 */
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

	/**
	 * Field group title.
	 *
	 * @return string
	 */
	protected function get_title(): string {
		return esc_html__( 'UCSC Photo of the Week Block', 'ucsc' );
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
	 * The heading, archive link and photo selector.
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		return [
			$this->get_title_field(),
			$this->get_cta_field( self::NAME, esc_html__( 'All Photos Link', 'ucsc' ) ),
			$this->get_photo_field(),
		];
	}

	/**
	 * Heading shown above the photo.
	 *
	 * @return array
	 */
	protected function get_title_field(): array {
		return [
			'type'  => 'text',
			'name'  => self::TITLE,
			'key'   => $this->get_field_key( self::TITLE, self::NAME ),
			'label' => esc_html__( 'Title', 'ucsc' ),
		];
	}

	/**
	 * The featured photo.
	 *
	 * Restricted to published Photo of the Week entries.
	 *
	 * @return array
	 */
	protected function get_photo_field(): array {
		return [
			'type'          => 'post_object',
			'name'          => self::PHOTO,
			'key'           => $this->get_field_key( self::PHOTO, self::NAME ),
			'label'         => esc_html__( 'Photo of the Week', 'ucsc' ),
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
