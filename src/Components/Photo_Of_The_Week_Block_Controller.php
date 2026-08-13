<?php
/**
 * Photo of the Week block controller.
 *
 * @package ucsc
 */

declare(strict_types=1);

namespace UCSC\Blocks\Components;

use UCSC\Blocks\Blocks\Photo_Of_The_Week_Block;
use UCSC\Blocks\Components\Traits\With_CTA;
use UCSC\Blocks\Components\Traits\With_Image_Size;
use UCSC\Blocks\Object_Meta\Photo_Of_The_Week_Meta;

/**
 * Prepares the single featured photo for the Photo of the Week block.
 *
 * The photograph and its credit live on the selected Photo of the Week post
 * as ACF meta, so both are read from that post rather than from the block.
 */
class Photo_Of_The_Week_Block_Controller {

	use With_Image_Size;
	use With_CTA;

	/**
	 * The block instance passed to the render callback.
	 *
	 * @var array
	 */
	protected array $block;
	/**
	 * The link to the full photo archive.
	 *
	 * @var array
	 */
	protected array $cta;

	/**
	 * Store the block instance and read the archive link.
	 *
	 * @param mixed $block The block instance supplied by the render callback.
	 */
	public function __construct( $block ) {
		$this->block = (array) $block;
		$this->cta   = (array) get_field( Photo_Of_The_Week_Block::CTA );
	}

	/**
	 * The block's wrapper attributes.
	 *
	 * @return string
	 */
	public function get_attributes(): string {
		return wp_kses_data(
			get_block_wrapper_attributes(
				[
					'class' => implode(
						' ',
						[
							'ucsc-photo-of-the-week-block',
							'alignfull',
							'has-black-background-color',
							'has-white-color',
							'has-global-padding',
							'is-layout-constrained',
						]
					),
				]
			)
		);
	}

	/**
	 * The block heading, or an empty string.
	 *
	 * @return string
	 */
	public function get_title(): string {
		$title = (string) get_field( Photo_Of_The_Week_Block::TITLE );

		return strlen( $title ) > 0 ? $title : '';
	}

	/**
	 * The featured photo, or null when none is selected.
	 *
	 * Bundles the rendered markup, a download URL, the title and the credit.
	 *
	 * @return array|null
	 */
	public function get_photo(): ?array {
		$photo = (string) get_field( Photo_Of_The_Week_Block::PHOTO );

		if ( empty( $photo ) ) {
			return null;
		}

		$image_data = $this->get_photo_image( $photo );

		if ( ! empty( $image_data ) ) {
			$image = sprintf(
				'<img src="%s" srcset="%s" alt="%s" class="photo-of-the-week__image" />',
				$image_data['url'],
				$this->build_srcset( $image_data ),
				get_the_title( get_the_ID() )
			);
		}

		return [
			'id'       => $photo,
			'image'    => $image ?: '',
			'download' => $image_data['url'] ?: '',
			'title'    => get_the_title( $photo ),
			'author'   => $this->get_photo_author( $photo ),
		];
	}

	/**
	 * The photographer credit for a photo.
	 *
	 * @param mixed $photo_id The Photo of the Week post ID.
	 *
	 * @return string
	 */
	public function get_photo_author( $photo_id ): string {
		return (string) get_field( Photo_Of_The_Week_Meta::PHOTOGRAPHER, $photo_id );
	}

	/**
	 * The photograph's attachment data, or an empty array.
	 *
	 * @param mixed $photo_id The Photo of the Week post ID.
	 *
	 * @return array
	 */
	public function get_photo_image( $photo_id ): array {
		$image = get_field( Photo_Of_The_Week_Meta::IMAGE, $photo_id );

		if ( empty( $image ) || $image['ID'] < 1 ) {
			return [];
		}

		$image_meta = wp_get_attachment_metadata( $image['ID'] );

		return array_merge(
			[
				'id'  => $image['ID'],
				'url' => $image['url'],
			],
			$image_meta
		);
	}
}
